<?php

namespace App\Jobs\Feeds;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncTeams implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    const LIMIT = 1000;

    public function handle(): void
    {

        $teams = Http::get(config('espn.teams').'?limit='.self::LIMIT)->json()['items'];

        $jobs = [];

        foreach ($teams as $team) {
            array_push($jobs, new SyncTeam($team['$ref']));
        }

        $batch = Bus::batch($jobs)
            ->name('Teams')
            ->dispatch();
    }
}
