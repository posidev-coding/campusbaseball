<?php

namespace App\Jobs\Feeds;

use App\Jobs\Feeds\SyncRanking;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncRankings implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            new WithoutOverlapping('sync.rankings')
        ];
    }

    public function handle(): void
    {

        // $rankings = Http::get(config('espn.rankings'))->json()['items'];
        $d1 = '/rankings/10543'; // D1Baseball.com rankings
        $base = config('espn.season').'/types/2/weeks/'; // regular season

        $jobs = [];

        for ($week = 1; $week <= 20; $week++) {
            $url = $base.$week.$d1;
            array_push($jobs, new SyncRanking($url));
        }

        $batch = Bus::batch($jobs)
            ->name('Rankings')
            ->allowFailures()
            ->dispatch();
    }
}
