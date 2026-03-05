<?php

namespace App\Jobs\Feeds;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGroups implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            (new WithoutOverlapping('sync.groups'))->dontRelease(),
        ];
    }

    public function handle(): void
    {

        $groups = Http::get(config('espn.groups').'?limit=100')->json()['items'];

        $jobs = [];

        foreach ($groups as $group) {
            $group_id = $this->extractId($group['$ref'], 'groups/');
            array_push($jobs, new SyncGroup($group_id, true));
        }

        $batch = Bus::batch($jobs)
            ->name('Conferences')
            ->dispatch();
    }

    public function extractId($url, $prefix): int
    {
        $path = strstr($url, $prefix);
        $withoutParams = strstr($path, '?', true);

        return intval(Str::of($withoutParams)->explode('/')[1]);
    }
}
