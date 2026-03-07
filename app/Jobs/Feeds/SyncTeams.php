<?php

namespace App\Jobs\Feeds;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncTeams implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            (new WithoutOverlapping('sync.teams'))->dontRelease(),
        ];
    }

    public function handle(): void
    {

        $teams = Http::get(config('espn.teams').'?limit=1000')->json()['items'];

        $jobs = [];

        foreach ($teams as $team) {
            $team_id = $this->extractId($team['$ref'], 'teams/');

            array_push($jobs, new SyncTeam($team_id));
        }

        $batch = Bus::batch($jobs)
            ->name('Teams')
            ->dispatch();
    }

    public function extractId($url, $prefix): int
    {
        $path = strstr($url, $prefix);
        $withoutParams = strstr($path, '?', true);

        return intval(Str::of($withoutParams)->explode('/')[1]);
    }
}
