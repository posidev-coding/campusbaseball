<?php

namespace App\Jobs\Feeds;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncTeams implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            new WithoutOverlapping('sync.teams')
        ];
    }

    public function handle(): void
    {

        $teams = Http::get(config('espn.teams').'?limit=1000')->json()['items'];

        $jobs = [];

        foreach ($teams as $team) {
            array_push($jobs, new SyncTeam($team['$ref']));
        }

        $batch = Bus::batch($jobs)
            ->name('Teams')
            ->dispatch();
    }
}
