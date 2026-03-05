<?php

namespace App\Jobs\Feeds;

use App\Models\Group;
use App\Models\Standing;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGroup implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    private $cascade;

    private $group_id;

    public function __construct(int $group, bool $cascade = false)
    {
        $this->group_id = $group;
        $this->url = config('espn.groups').'/'.$group;
        $this->cascade = $cascade;
    }

    public function middleware(): array
    {
        $jobKey = $this->cascade ? "sync.group.{$this->group_id}.cascade" : "sync.group.{$this->group_id}";
        return [
            new SkipIfBatchCancelled,
            (new WithoutOverlapping($jobKey))->dontRelease(),
        ];
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

        $this->standings($data);

        if ($this->cascade && isset($data['children']['$ref'])) {

            // get and add jobs
            $jobs = [];

            $groups = Http::get($data['children']['$ref'])->json()['items'];

            foreach ($groups as $group) {
                $group_id = $this->extractId($group['$ref'], 'groups/');
                array_push($jobs, new SyncGroup($group_id, $this->cascade));
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

    public function extractId($url, $prefix): int
    {
        $path = strstr($url, $prefix);
        $withoutParams = strstr($path, '?', true);

        return intval(Str::of($withoutParams)->explode('/')[1]);
    }

    public function standings(mixed $data): void
    {

        if(!isset($data['standings']['$ref'])) {
            return;
        }
        
        $conf_id = $data['id'];
        
        $standings = Http::get($data['standings']['$ref'])->json()['items'][0]['$ref'];
        
        $standings = Http::get($standings)->json();

        if(!isset($standings['standings'])) {
            return;
        }

        $standings = $standings['standings'];

        foreach($standings as $key => $team) {

            $team_id = $this->extractId($team['team']['$ref'], 'teams/');

            // upsert the standing model
            $standing = Standing::updateOrCreate(
                [
                    'conference_id' => $conf_id,
                    'team_id' => $team_id,
                ],
                [
                    'ranking' => $key + 1,
                    'record' => $team['records'][0]['summary'] ?? null,
                    'stats' => $team['records'][0]['stats'] ?? null
                ]
            );

            // sync team job
            SyncTeam::dispatch($team_id, $conf_id);

        }

    }
}
