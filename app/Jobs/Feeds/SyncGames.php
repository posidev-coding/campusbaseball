<?php

namespace App\Jobs\Feeds;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGames implements ShouldQueue
{
    use Queueable;

    const LIMIT = 1000;

    private array $events;

    private $calendarDate;

    public function __construct($calendarDate = null)
    {
        $this->calendarDate = $calendarDate;
    }

    public function handle(): void
    {

        $jobs = [];
        $dates = [];

        if ($this->calendarDate) {

            array_push($dates, $this->calendarDate);
        } else {
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->limit(5)
                ->pluck('calendar_date');
        }

        foreach ($dates as $date) {

            $url = config('espn.games').'?dates='.Carbon::parse($date)->format('Ymd').'&limit='.self::LIMIT;

            $games = Http::get($url)->json()['items'];

            foreach ($games as $game) {
                $id = Str::of(Str::chopStart($game['$ref'], config('espn.games').'/'))->explode('?')[0];
                array_push($jobs, new SyncGame($id));
            }
        }

        $batch = Bus::batch($jobs)
            ->name('Games')
            ->allowFailures()
            ->dispatch();
    }
}
