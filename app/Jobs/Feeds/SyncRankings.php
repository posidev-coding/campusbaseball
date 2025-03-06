<?php

namespace App\Jobs\Feeds;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class SyncRankings implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {

        $rankings = Http::get(config('espn.rankings'))->json()['items'];

        $jobs = [];

        foreach ($rankings as $ranking) {
            array_push($jobs, new SyncRanking($ranking['$ref']));
        }

        $batch = Bus::batch($jobs)
            ->name('Rankings')
            ->allowFailures()
            ->dispatch();
    }
}
