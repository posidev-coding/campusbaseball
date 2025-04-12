<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use App\Models\NCAAGame;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class VerifyNCAAGames implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }
    
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
        
        $today = today('America/New_York');

        $dates = [
            $today->format('Ymd'),
            $today->subDay()->format('Ymd'),
            $today->addDay()->format('Ymd')
        ];

        $validIds = [];

        foreach ($dates as $date) {

            $urlDate = Carbon::parse($date)->format('m/d/Y');
            $url = config('ncaa.games').'&variables=' . urlencode('{"sportCode":"MBA","division":1,"seasonYear":' . config('ncaa.year') . ',"contestDate":"' . $urlDate . '","week":null}');
            $contests = Http::get($url)->json()['data']['contests'];

            foreach ($contests as $contest) {
                if($contest['sportUrl'] == 'baseball') {
                    array_push($validIds, $contest['contestId']);
                }
            }

        }

        $games = NCAAGame::whereIn('game_date', $dates)->get();

        foreach($games as $game) {
            if(!in_array($game->id, $validIds)) {
                // game id reassigned or something
                
                $game->delete();
                
                if($match = Game::where('ncaa_id', $game->id)->first()) {
                    $match->ncaa_id = null;
                    $match->save();
                }
                
                Log::info('Deleted NCAA Game: ' . $game->id);
            }
        }

    }
}
