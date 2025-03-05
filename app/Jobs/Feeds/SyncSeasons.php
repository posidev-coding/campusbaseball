<?php

namespace App\Jobs\Feeds;

use App\Models\Season;
use App\Models\SeasonType;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SyncSeasons implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seasons = Http::get(config('espn.seasons'))->json()['items'];

        foreach ($seasons as $season) {
            $s = Http::get($season['$ref'])->json();

            $start = Carbon::parse($s['startDate'], 'UTC');
            $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
            $end = Carbon::parse($s['endDate'], 'UTC');
            $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

            $model = Season::updateOrInsert(
                [
                    'id' => $s['year'],
                ],
                [
                    'name' => $s['displayName'],
                    'description' => $s['description'],
                    'type_id' => $s['type']['id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'refs' => [
                        'self' => $s['$ref'],
                        'type' => $s['type']['$ref'],
                        'types' => $s['types']['$ref'],
                    ],
                ]
            );

            $sTypes = $s['types']['items'];

            foreach ($sTypes as $sType) {

                $start = Carbon::parse($sType['startDate'], 'UTC');
                $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
                $end = Carbon::parse($sType['endDate'], 'UTC');
                $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

                $seasonType = SeasonType::updateOrInsert(
                    [
                        'type_id' => $sType['id'],
                        'season_id' => $s['year'],
                    ],
                    [
                        'name' => $sType['name'],
                        'slug' => $sType['slug'],
                        'abbreviation' => $sType['abbreviation'],
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'has_groups' =>  $sType['hasGroups'],
                        'has_standings' => $sType['hasStandings'],
                        'has_legs' => $sType['hasLegs'],
                        'refs' => [
                            'self' => $sType['$ref'],
                            'groups' => $sType['groups']['$ref'],
                            'weeks' => $sType['weeks']['$ref'],
                        ],
                    ]
                );
            }
        }

        $current = Http::get(config('espn.about'))->json()['season'];

        $start = Carbon::parse($current['startDate'], 'UTC');
        $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $end = Carbon::parse($current['endDate'], 'UTC');
        $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

        $currentSeason = Season::updateOrInsert(
            [
                'id' => $current['year'],
            ],
            [
                'name' => $current['displayName'],
                'description' => $current['description'],
                'type_id' => $current['type']['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'refs' => [
                    'self' => $current['$ref'],
                    'type' => $current['type']['$ref'],
                    'types' => $current['types']['$ref'],
                ],
            ]
        );
    }
}
