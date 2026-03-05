<?php

namespace App\Jobs\Feeds;

use App\Models\NCAATeam;
use App\Models\Record;
use App\Models\Stat;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncTeam implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    private $url;

    private $team_id;

    private $conf_id;

    public function __construct(int $team, $assignToConference = null)
    {
        $this->team_id = $team;
        $this->conf_id = $assignToConference;
        $this->url = config('espn.teams').'/'.$team;
    }

    public function middleware(): array
    {
        $jobKey = $this->conf_id ? "sync.team.{$this->team_id}.{$this->conf_id}" : "sync.team.{$this->team_id}";
        return [
            new SkipIfBatchCancelled,
            (new WithoutOverlapping($jobKey))->dontRelease(),
        ];
    }

    public function handle(): void
    {

        $data = Http::get($this->url)->json();

        if (isset($data['groups']) && !$this->conf_id) {
            $group = Http::get($data['groups']['$ref'])->json();

            if (! $group['isConference'] && isset($group['parent'])) {
                $parent = Http::get($group['parent']['$ref'])->json();

                if ($parent['isConference']) {
                    $this->conf_id = $parent['id'];
                }
            } else {
                $this->conf_id = $group['id'];
            }
        }

        $team = Team::findOr($this->team_id, function () use ($data) {
            return Team::create([
                'id' => $this->team_id,
                'conference_id' => $this->conf_id,
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
            $team->conference_id = $team->conference_id ?? $this->conf_id;

            $team->save();
        }

        if(!$team->ncaa_id) {
            Log::info('Matching team: ' . $team->location);
            $this->ncaa($team);
        } else {
            Log::info('Team already matched: ' . $team->location);
        }

        $this->records();
    }

    public function ncaa($team)
    {
        // Find and associate an NCAA Team ID
        $ncaa = NCAATeam::where('slug', $team->slug)
                            ->orWhere('short_name', $team->location)
                            ->orWhere('short_name', $team->nickname)
                            ->orWhere('short_name', $team->display_name)
                            ->orWhere('short_name', $team->short_display_name)
                            ->get();

        // Associate if found only one match                
        if(count($ncaa) == 1) {
            $team->ncaa_id = $ncaa[0]->id;
            $team->save();
        };
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
                $pct = ($wins + $losses) > 0 ? round(($wins / ($wins + $losses)), 3) : 0;

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
