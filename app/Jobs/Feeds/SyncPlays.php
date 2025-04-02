<?php

namespace App\Jobs\Feeds;

use App\Events\NewPlays;
use App\Models\Game;
use App\Models\Play;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncPlays implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    private int $limit = 2;

    private Game $game;

    private int $playCursor;

    private int $pageCursor;

    private int $playCount;

    public function __construct(int $game, $all = false)
    {
        $this->game = Game::find($game);
        $this->pageCursor = $all ? 1 : ($this->game->play_page ?? 1);
        $this->playCursor = $this->game->play_cursor ?? 0;
        $this->playCount = 0;
    }

    public function handle(): void
    {

        if ($plays = $this->paginate()) {
            if ($this->playCursor > 0) {
                $this->game->play_page = $this->pageCursor;
                $this->game->play_cursor = $this->playCursor;
                $this->game->save();
            }

            if ($this->playCount > 0) {
                NewPlays::dispatch($this->game->id);
            }
        }
    }

    public function extractId($url, $prefix): int
    {
        $path = strstr($url, $prefix);
        $withoutParams = strstr($path, '?', true);

        return intval(Str::of($withoutParams)->explode('/')[1]);
    }

    // recursive
    public function paginate()
    {

        if (! isset($this->game->resources['plays'])) {
            return false;
        }

        $data = Http::get($this->game->resources['plays'].'&limit=50&page='.$this->pageCursor)->json();

        foreach ($data['items'] as $play) {

            // only upsert plays after the game cursor
            if ($play['id'] > $this->game->play_cursor) {

                $team_id = $this->extractId($play['team']['$ref'], 'teams/');

                $pitcher_id = null;
                $batter_id = null;
                $runners = [];

                if (isset($play['participants'])) {
                    foreach ($play['participants'] as $athlete) {
                        if ($athlete['type'] == 'pitcher') {
                            $pitcher_id = $this->extractId($athlete['athlete']['$ref'], 'athletes/');
                        } elseif ($athlete['type'] == 'batter') {
                            $batter_id = $this->extractId($athlete['athlete']['$ref'], 'athletes/');
                        } elseif (in_array($athlete['type'], ['onFirst', 'onSecond', 'onThird'])) {
                            array_push($runners, $athlete['type']);
                        }
                    }
                }

                $model = Play::updateOrCreate(
                    [
                        'id' => $play['id'],
                    ],
                    [
                        'game_id' => $this->game->id,
                        'team_id' => $team_id,
                        'atbat_id' => intval($play['atBatId']),
                        'pitcher_id' => $pitcher_id,
                        'batter_id' => $batter_id,
                        'runners' => $runners,
                        'sequence' => intval($play['sequenceNumber']),
                        'inning' => $play['period']['number'],
                        'inning_type' => $play['period']['type'],
                        'inning_display' => $play['period']['displayValue'],
                        'type_id' => $play['type']['id'],
                        'type_text' => $play['type']['text'],
                        'text' => $play['text'] ?? '(Type) '.$play['type']['text'],
                        'scoring_play' => $play['scoringPlay'] ?? false,
                        'outs' => intval($play['outs']) ?? 0,
                        'score_value' => intval($play['scoreValue']) ?? 0,
                        'away_runs' => intval($play['awayScore']) ?? 0,
                        'away_hits' => intval($play['awayHits']) ?? 0,
                        'away_errors' => intval($play['awayErrors']) ?? 0,
                        'home_runs' => intval($play['homeScore']) ?? 0,
                        'home_hits' => intval($play['homeHits']) ?? 0,
                        'home_errors' => intval($play['homeErrors']) ?? 0,
                    ]
                );
                $this->playCount++;
            }

            // only advance cursor once it has been stored
            $this->playCursor = $play['id'];
        }

        if ($data['pageIndex'] < $data['pageCount']) {
            $this->pageCursor++;
            $this->paginate();
        } else {
            return true;
        }
    }
}
