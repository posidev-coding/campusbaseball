<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\Calendar;
use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncGames implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Queueable;

    const LIMIT = 1000;

    private array $events;

    private string $date;

    private string $mode;

    private $presets = [
        'today', // live
        'tomorrow', // full
        'future', // full
        'yesterday', // final
        'past', // final
        'full', // final
    ];

     /**
      * Get the unique ID for the job.
      */
     public function uniqueId(): string
     {
         return $this->date . '.' . $this->mode;
     }

    public function __construct($date = 'today')
    {
        $this->date = $date;
        $this->mode = in_array($date, ['full', 'past', 'yesterday']) ? 'final' : (in_array($date, ['tomorrow', 'future']) ? 'full' : 'live');
    }

    public function handle(): void
    {

        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $jobs = [];

        if ($this->date == 'live') {
            // pull individual games from the database
            $games = Game::where(function (Builder $query) {
                // Unfinalized & live
                $query->where('status_id', 2)->where('finalized', 0);
            })->orWhere(function (Builder $query) {
                // Or current date and game_time in past
                $query->where('game_date', today())->where('game_time', '<=', now());
            })->get();

            foreach ($games as $game) {
                array_push($jobs, new SyncGame($game->id, $this->mode));
            }
        } else {
            // get dates & paginate the api
            $dates = $this->getDates();
            foreach ($dates as $date) {
                $url = config('espn.games').'?dates='.Carbon::parse($date)->format('Ymd').'&limit='.self::LIMIT;
                $games = Http::get($url)->json()['items'];
                foreach ($games as $game) {
                    $id = Str::of(Str::chopStart($game['$ref'], config('espn.games').'/'))->explode('?')[0];
                    array_push($jobs, new SyncGame($id, $this->mode));
                }
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
