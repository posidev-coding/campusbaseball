<?php

namespace App\Jobs\Feeds;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncGroups implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    const LIMIT = 100;

    public function handle(): void
    {

        $groups = Http::get(config('espn.groups').'?limit='.self::LIMIT)->json()['items'];

        $jobs = [];

        foreach ($groups as $group) {
            array_push($jobs, new SyncGroup($group['$ref'], true));
        }

        $batch = Bus::batch($jobs)
            ->name('Conferences')
            ->dispatch();
    }
}
