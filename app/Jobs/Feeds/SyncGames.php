<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\Calendar;
use Illuminate\Support\Str;
use App\Jobs\Feeds\SyncGame;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncGames implements ShouldQueue
{
    use Batchable, Queueable;

    private array $events;

    private string $date;

    private string $mode;

    private string $jobName;

    private string $batchKey;

    private $presets = [
        'today', // live
        'tomorrow', // full
        'future', // full
        'yesterday', // final
        'past', // final
        'full', // final
    ];

    public function __construct($date = 'today', $scheduleName = null)
    {
        $this->date = $date;
        $this->mode = in_array($date, ['full', 'past', 'yesterday']) ? 'final' : (in_array($date, ['tomorrow', 'future']) ? 'full' : 'live');
        $this->batchKey = 'Games.' . $this->date . '.' . $this->mode;
        if($scheduleName) $this->batchKey .= '.scheduled';
        $this->jobName = $scheduleName ?? $this->batchKey;
        Log::info('Queued Games Sync: ' . $this->jobName);
    }

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled,
            new WithoutOverlapping($this->batchKey)
        ];
    }

    public function handle(): void
    {

        Log::info('Running Games Sync: ' . $this->jobName);

        $jobs = [];

        if ($this->date == 'live') {
            // pull individual games from the database
            $games = Game::where(function (Builder $query) {
                // Unfinalized & live
                $query->where('status_id', 2)->where('finalized', 0);
            })->orWhere(function (Builder $query) {
                // Or current date and game_time in past
                $query->where('game_date', today())->where('game_time', '<=', now())->where('finalized', 0);
            })->get();

            foreach ($games as $game) {
                array_push($jobs, new SyncGame($game->id, $this->mode));
            }

        } else {
            // get dates & paginate the api
            $dates = $this->getDates();
            foreach ($dates as $date) {
                $url = config('espn.games').'?dates='.Carbon::parse($date)->format('Ymd').'&limit=1000';
                $games = Http::get($url)->json()['items'];
                foreach ($games as $game) {
                    $id = Str::of(Str::chopStart($game['$ref'], config('espn.games').'/'))->explode('?')[0];
                    array_push($jobs, new SyncGame($id, $this->mode));
                }
            }
        }

        if(count($jobs) > 0) {
            if ($this->batch() && ! $this->batch()->cancelled()) {
                $this->batch()->add($jobs);
            } else {
                Bus::batch($jobs)
                    ->name($this->jobName)
                    ->dispatch();
            }
            Log::info($this->jobName . ': Batched out ' . count($jobs) . ' games to sync');
        } else {
            Log::info($this->jobName . ': No games to sync..');
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
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->where('calendar_date', '>=', today('America/New_York'))
                ->pluck('calendar_date');
        }

        if ($this->date == 'past') {
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->where('calendar_date', '<=', today('America/New_York'))
                ->pluck('calendar_date');
        }

        if ($this->date == 'full') {
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->pluck('calendar_date');
        }

        return $dates;
    }
}
