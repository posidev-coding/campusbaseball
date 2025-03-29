<?php

namespace App\Jobs\Feeds;

use App\Models\Calendar;
use App\Models\Season;
use App\Models\SeasonType;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SyncCalendar implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 1;

    public function handle(): void
    {

        $this->seasons();

        $calendars = Http::get(config('espn.calendar'))->json()['items'];

        foreach ($calendars as $calendar) {

            $cal = Http::get($calendar['$ref'])->json();

            $season_id = Http::get($cal['season']['$ref'])->json()['year'];

            $calType = $cal['eventDate']['type'];

            foreach ($cal['eventDate']['dates'] as $calDate) {

                $dt = Carbon::parse($calDate)->format('Y-m-d');

                $model = Calendar::updateOrCreate(
                    [
                        'season_id' => $season_id,
                        'calendar_type' => $calType,
                        'calendar_date' => $dt,
                    ],
                    [
                        'season_id' => $season_id,
                        'calendar_type' => $calType,
                        'calendar_date' => $dt,
                    ]
                );
            }
        }
    }

    public function seasons()
    {
        $seasons = Http::get(config('espn.seasons'))->json()['items'];

        foreach ($seasons as $season) {
            $s = Http::get($season['$ref'])->json();

            $start = Carbon::parse($s['startDate'], 'UTC');
            $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
            $end = Carbon::parse($s['endDate'], 'UTC');
            $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

            $model = Season::updateOrCreate(
                [
                    'id' => $s['year'],
                ],
                [
                    'name' => $s['displayName'],
                    'description' => $s['description'],
                    'type_id' => $s['type']['id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                ]
            );

            $sTypes = $s['types']['items'];

            foreach ($sTypes as $sType) {

                $start = Carbon::parse($sType['startDate'], 'UTC');
                $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
                $end = Carbon::parse($sType['endDate'], 'UTC');
                $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

                $seasonType = SeasonType::updateOrCreate(
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
                        'has_groups' => $sType['hasGroups'],
                        'has_standings' => $sType['hasStandings'],
                        'has_legs' => $sType['hasLegs'],
                    ]
                );
            }
        }

        $current = Http::get(config('espn.base'))->json()['season'];

        $start = Carbon::parse($current['startDate'], 'UTC');
        $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $end = Carbon::parse($current['endDate'], 'UTC');
        $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $end);

        $currentSeason = Season::updateOrCreate(
            [
                'id' => $current['year'],
            ],
            [
                'name' => $current['displayName'],
                'description' => $current['description'],
                'type_id' => $current['type']['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]
        );
    }
}
