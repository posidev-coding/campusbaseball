<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Calendar;
use Illuminate\Bus\Batchable;
use App\Jobs\Feeds\SyncNCAAGame;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncNCAAGames implements ShouldQueue
{
    use Batchable, Queueable;

    private string $date;
    private string $jobName;
    private string $batchKey;

    private $presets = [
        'today',
        'tomorrow',
        'future',
        'yesterday',
        'past',
        'all'
    ];

    public function __construct($date = 'today', $scheduleName = null)
    {
        $this->date = $date;
        $this->batchKey = 'NCAAGames.' . $this->date;
        if($scheduleName) $this->batchKey .= '.scheduled';
        $this->jobName = $scheduleName ?? $this->batchKey;
        Log::info('Queued NCAA Games Sync: ' . $this->jobName);
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
        
        Log::info('Running NCAA Games Sync: ' . $this->jobName);

        $jobs = [];

        $dates = $this->getDates();

        foreach ($dates as $date) {

            $urlDate = Carbon::parse($date)->format('m/d/Y');

            $url = config('ncaa.games').'&variables=' . urlencode('{"sportCode":"MBA","division":1,"seasonYear":' . config('ncaa.year') . ',"contestDate":"' . $urlDate . '","week":null}');

            $games = Http::get($url)->json()['data']['contests'];

            foreach ($games as $game) {
                array_push($jobs, new SyncNCAAGame($game['contestId']));
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

        if ($this->date == 'all') {
            $dates = Calendar::where('season_id', config('espn.year'))
                ->where('calendar_type', 'ondays')
                ->pluck('calendar_date');
        }

        return $dates;
    }

}
