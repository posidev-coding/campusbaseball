<?php

namespace App\Jobs\Feeds;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGames implements ShouldQueue
{
    use Batchable, Queueable;

    const LIMIT = 1000;

    private array $events;

    private string $date;

    private $presets = [
        'today',
        'tomorrow',
        'yesterday',
        'future',
        'past',
        'full',
    ];

    public function __construct($date = 'today')
    {
        $this->date = $date;
    }

    public function handle(): void
    {

        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $jobs = [];
        $dates = $this->getDates();

        foreach ($dates as $date) {

            $url = config('espn.games').'?dates='.Carbon::parse($date)->format('Ymd').'&limit='.self::LIMIT;

            $games = Http::get($url)->json()['items'];

            foreach ($games as $game) {
                $id = Str::of(Str::chopStart($game['$ref'], config('espn.games').'/'))->explode('?')[0];
                array_push($jobs, new SyncGame($id));
            }
        }

        if ($this->batch() && ! $this->batch()->cancelled()) {
            $this->batch()->add($jobs);
        } else {
            Bus::batch($jobs)
                ->name('Games '.$this->date)
                ->dispatch();
        }
    }

    public function getDates()
    {

        $dates = [];

        if (! in_array($this->date, $this->presets)) {
            // specific date provided
            array_push($dates, $this->date);

            return $dates;
        }

        if ($this->date == 'today') {
            array_push($dates, today('America/New_York')->format('Ymd'));
        }

        if ($this->date == 'yesterday') {
            array_push($dates, today('America/New_York')->subDay()->format('Ymd'));
        }

        if ($this->date == 'tomorrow') {
            array_push($dates, today('America/New_York')->addDay()->format('Ymd'));
        }

        if ($this->date == 'future') {
            SyncCalendar::dispatchSync();
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->where('calendar_date', '>=', today('America/New_York'))
                ->pluck('calendar_date');
        }

        if ($this->date == 'past') {
            SyncCalendar::dispatchSync();
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->where('calendar_date', '<=', today('America/New_York'))
                ->pluck('calendar_date');
        }

        if ($this->date == 'full') {
            SyncCalendar::dispatchSync();
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->pluck('calendar_date');
        }

        return $dates;
    }
}
