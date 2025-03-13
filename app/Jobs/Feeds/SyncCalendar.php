<?php

namespace App\Jobs\Feeds;

use App\Models\Calendar;
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

        $calendars = Http::get(config('espn.calendar'))->json()['items'];

        foreach ($calendars as $calendar) {

            $cal = Http::get($calendar['$ref'])->json();

            $season_id = Http::get($cal['season']['$ref'])->json()['year'];

            // dd($season_id);

            $calType = $cal['eventDate']['type'];

            foreach ($cal['eventDate']['dates'] as $calDate) {

                $dt = Carbon::parse($calDate)->format('Y-m-d');

                $model = Calendar::firstOrNew(
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

                $model->save();
            }
        }
    }
}
