<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\NCAAGame;
use App\Models\NCAATeam;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncNCAAGame implements ShouldQueue
{

    use Batchable, Queueable;

    public $tries = 1;
    private $game;
    private $url;

    public function __construct(int $game)
    {
        $this->game = $game;
        $this->url = config('ncaa.game') . '&variables=' . urlencode('{"id":"' . $this->game . '","week":null,"staticTestEnv":null}');
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        if (isset($data['data']['contests'][0])) {

            $game = $data['data']['contests'][0];

            $localDate = Carbon::parse($game['startTimeEpoch'])->shiftTimezone('UTC')->setTimezone('America/New_York');

            $home = $game['teams'][0]['isHome'] ? $game['teams'][0] : $game['teams'][1];
            $away = $game['teams'][0]['isHome'] ? $game['teams'][1] : $game['teams'][0];

            $awayTeam = NCAATeam::updateOrCreate(
                [
                    'id' => $away['teamId']
                ],
                [
                    'slug' => $away['seoname'],
                    'short_name' => substr($away['nameShort'], -1) == '.' ? substr($away['nameShort'], 0, -1) : $away['nameShort'],
                    'full_name' => $away['nameFull'],
                ]
            );

            $homeTeam = NCAATeam::updateOrCreate(
                [
                    'id' => $home['teamId']
                ],
                [
                    'slug' => $home['seoname'],
                    'short_name' => substr($home['nameShort'], -1) == '.' ? substr($home['nameShort'], 0, -1) : $home['nameShort'],
                    'full_name' => $home['nameFull'],
                ]
            );

            // upsert the standing model
            $ncaa = NCAAGame::updateOrCreate(
                [
                    'id' => $this->game
                ],
                [
                    'game_date' => $localDate->toDateString(),
                    'game_time' => $localDate,
                    'game_state' => $game['gameState'] ?? null,
                    'status_code' => $game['statusCodeDisplay'] ?? null,
                    'away_id' => $away['teamId'],
                    'home_id' => $home['teamId'],
                    'boxscore_available' => $game['hasBoxscore'],
                    'summary_available' => $game['hasScoringSummary'],
                    'pbp_available' => $game['hasPbp'],
                    'linescores' => $game['linescores'] ?? null,
                    'stats' => $game['stats'] ?? null
                ]
            );

            $this->espn($ncaa);
        }
    }

    // Find and assign NCAA IDs to ESPN games
    public function espn($ncaa)
    {

        $exact = Game::where('game_date', $ncaa['game_date'])
            ->where(function (Builder $builder) use ($ncaa) {
                $builder->whereHas('home', function (Builder $query) use ($ncaa) {
                    $query->where('ncaa_id', $ncaa->home_id);
                })
                    ->orWhereHas('away', function (Builder $query) use ($ncaa) {
                        $query->where('ncaa_id', $ncaa->away_id);
                    });
            })->get();

        if ($exact && count($exact) == 1) {
            $espn = $exact[0];
            $espn->ncaa_id = $ncaa['id'];
            $espn->save();
            return;
        }

        $homeMatch = Game::where('game_date', $ncaa['game_date'])
            ->whereHas('home', function (Builder $query) use ($ncaa) {
                $query->where('ncaa_id', $ncaa->home_id);
            })->get();

        if ($homeMatch && count($homeMatch) == 1) {
            $espn = $homeMatch[0];
            $espn->ncaa_id = $ncaa['id'];
            $espn->save();
            return;
        }
    }
}
