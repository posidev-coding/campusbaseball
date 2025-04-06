<?php

namespace App\Jobs\Feeds;

use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncRankings implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

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
