<?php

namespace App\Jobs\Feeds;

use App\Models\Group;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGroup implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    private $cascade;

    public function __construct(string $req, bool $cascade = false)
    {
        $this->url = Str::isUrl($req) ? $req : config('espn.groups').'/'.$req;
        $this->cascade = $cascade;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        $parent_id = isset($data['parent']) ? Http::get($data['parent']['$ref'])->json()['id'] : null;

        $model = Group::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'name' => $data['name'] ?? null,
                'abbreviation' => $data['abbreviation'] ?? null,
                'short_name' => $data['shortName'] ?? null,
                'midsize_name' => $data['midsizeName'] ?? null,
                'is_conference' => $data['isConference'] ?? 0,
                'parent_id' => $parent_id,
            ]
        );

        if ($this->cascade && isset($data['children']['$ref'])) {

            // get and add jobs
            $jobs = [];

            $groups = Http::get($data['children']['$ref'])->json()['items'];

            foreach ($groups as $group) {
                array_push($jobs, new SyncGroup($group['$ref'], $this->cascade));
            }

            if ($this->batch() && ! $this->batch()->cancelled()) {
                $this->batch()->add($jobs);
            } else {
                $batch = Bus::batch($jobs)
                    ->name('Conferences')
                    ->allowFailures()
                    ->dispatch();
            }
        }
    }
}
