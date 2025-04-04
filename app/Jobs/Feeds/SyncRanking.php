<?php

namespace App\Jobs\Feeds;

use App\Models\Ranking;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Http;

class SyncRanking implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $poll = Http::get($this->url)->json();

        $ranks = $poll['ranks'] ?? [];

        foreach ($ranks as $rank) {

            $team_id = Http::get($rank['team']['$ref'])->json()['id'];

            if ($team_id == 514) {
                $team_id = 263;
            }

            $ranking = Ranking::updateOrCreate(
                [
                    'season_id' => $poll['season']['year'],
                    'season_type_id' => $poll['season']['type']['id'],
                    'week_nbr' => $poll['occurrence']['number'],
                    'team_id' => $team_id,
                ],
                [
                    'week_display' => $poll['occurrence']['displayValue'],
                    'headline' => $poll['headline'],
                    'current' => $rank['current'] ?? null,
                    'previous' => $rank['previous'] ?? null,
                    'trend' => $rank['trend'] ?? null,
                ]
            );

            $team = Team::findOr($team_id, function () use ($team_id) {
                SyncTeam::dispatch($team_id);
            });

        }
    }
}
