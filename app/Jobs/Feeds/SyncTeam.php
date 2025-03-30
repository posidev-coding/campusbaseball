<?php

namespace App\Jobs\Feeds;

use App\Models\Record;
use App\Models\Stat;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncTeam implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    private $team_id;

    public function __construct(string $req)
    {
        $this->url = Str::isUrl($req) ? $req : config('espn.teams').'/'.$req;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        $this->team_id = $data['id'];

        $conf_id = null;

        if (isset($data['groups'])) {
            $group = Http::get($data['groups']['$ref'])->json();

            if (! $group['isConference'] && isset($group['parent'])) {
                $parent = Http::get($group['parent']['$ref'])->json();

                if ($parent['isConference']) {
                    $conf_id = $parent['id'];
                }
            } else {
                $conf_id = $group['id'];
            }
        }

        $team = Team::findOr($this->team_id, function () use ($data, $conf_id) {
            return Team::create([
                'id' => $this->team_id,
                'conference_id' => $conf_id,
                'slug' => $data['slug'] ?? null,
                'location' => $data['location'] ?? null,
                'name' => $data['name'] ?? null,
                'nickname' => $data['nickname'] ?? null,
                'abbreviation' => $data['abbreviation'] ?? null,
                'display_name' => $data['displayName'] ?? null,
                'short_display_name' => $data['shortDisplayName'] ?? null,
                'color' => $data['color'] ?? null,
                'logos' => $data['logos'] ?? null,
            ]);
        });

        if (! $team->wasRecentlyCreated) {
            // Found model in database, update it
            $team->slug = $data['slug'] ?? null;
            $team->location = $data['location'] ?? null;
            $team->name = $data['name'] ?? null;
            $team->nickname = $data['nickname'] ?? null;
            $team->abbreviation = $data['abbreviation'] ?? null;
            $team->display_name = $data['displayName'] ?? null;
            $team->short_display_name = $data['shortDisplayName'] ?? null;
            $team->color = $data['color'] ?? null;
            $team->logos = $data['logos'] ?? null;

            // Dont overwrite the conference ID once it's been set
            // Many teams need manual conference assignment because the ESPN API
            // Doesn't specificy one
            $team->conference_id = $team->conference_id ?? $conf_id;

            $team->save();
        }

        $this->records();
    }

    public function records()
    {

        // Get Records & Stats
        $records = Http::get(config('espn.season').'/types/2/teams/'.$this->team_id.'/record')->json();

        if (isset($records['items'])) {

            foreach ($records['items'] as $item) {

                $scope = $item['type'] == 'total' ? 'overall' : ($item['type'] == 'road' ? 'away' : $item['type']);

                $counts = explode('-', $item['summary']);

                $wins = intval($counts[0]);
                $losses = intval($counts[1]);
                $pct = round(($wins / ($wins + $losses)), 3);

                $record = Record::updateOrCreate(
                    [
                        'team_id' => $this->team_id,
                        'scope' => $scope,
                    ],
                    [
                        'summary' => $item['summary'] ?? null,
                        'wins' => $wins,
                        'losses' => $losses,
                        'pct' => $pct,
                    ]
                );

                if (isset($item['stats'])) {

                    foreach ($item['stats'] as $stat) {

                        $stat = Stat::updateOrCreate(
                            [
                                'team_id' => $this->team_id,
                                'scope' => $scope,
                                'name' => $stat['name'],
                            ],
                            [
                                'display_name' => $stat['displayName'] ?? null,
                                'short_display_name' => $stat['shortDisplayName'] ?? null,
                                'description' => $stat['description'] ?? null,
                                'abbreviation' => $stat['abbreviation'] ?? null,
                                'stat_value' => $stat['value'] ?? null,
                                'display_value' => $stat['displayValue'] ?? null,
                            ]
                        );
                    }
                }
            }
        }
    }
}
