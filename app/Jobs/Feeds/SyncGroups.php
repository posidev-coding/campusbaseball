<?php

namespace App\Jobs\Feeds;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncGroups implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            new WithoutOverlapping('sync.groups')
        ];
    }

    public function handle(): void
    {

        $groups = Http::get(config('espn.groups').'?limit=100')->json()['items'];

        $jobs = [];

        foreach ($groups as $group) {
            array_push($jobs, new SyncGroup($group['$ref'], true));
        }

        $batch = Bus::batch($jobs)
            ->name('Conferences')
            ->dispatch();
    }
}
