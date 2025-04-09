<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\NCAAGame;
use App\Models\NCAATeam;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        $this->url = config('ncaa.game'). '&variables=' . urlencode('{"id":"' . $this->game . '","week":null,"staticTestEnv":null}');
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        if(isset($data['data']['contests'][0])) {

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

        }

    }
    
    /*
    $matches = Game::where('game_date', $date)
                ->where(function(Builder $query) use($away_team, $home_team){
                    $query->whereHas('home', function(Builder $q) use($home_team) {
                        $q->where('location', 'like', "%{$home_team}%")
                            ->orWhere('nickname', 'like', "%{$home_team}%");
                    })->orWhereHas('away', function(Builder $q) use($away_team) {
                        $q->where('location', 'like', "%{$away_team}%")
                            ->orWhere('nickname', 'like', "%{$away_team}%");
                    });
                })
                ->get();
                */
}
