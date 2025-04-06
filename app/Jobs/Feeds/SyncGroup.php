<?php

namespace App\Jobs\Feeds;

use App\Models\Group;
use App\Models\Team;
use App\Models\Standing;
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

        $this->standings($data);

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
            SyncTeam::dispatch($team['team']['$ref'], $conf_id);

        }

    }
}
