<?php

namespace App\Jobs\Feeds;

use Carbon\Carbon;
use App\Models\Game;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class NCAAGame implements ShouldQueue
{

    use Batchable, Queueable;

    public $tries = 3;
    private $game;
    private $url;

    public function __construct(int $game)
    {
        $this->game = $game;
        $this->url = config('ncaa.game') . '/' . $game;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {

        if($game = Http::get($this->url)->json()['data']) {

            $date = Carbon::parse($game['game_date'])->toDateString();

            $ncaa_id = $game['id'];
            $away_team = str_replace('.','',$game['competitors'][0]['nameTabular']);
            $home_team = str_replace('.','',$game['competitors'][1]['nameTabular']);

            $matches = Game::where('game_date', $date)
                            ->where(function(Builder $query) use($away_team, $home_team){
                                $query->whereHas('home', function(Builder $q) use($home_team) {
                                    $q->where('location', 'like', "%{$home_team}%")
                                        ->orWhere('nickname', 'like', "%{$home_team}%");
                                })->orWhereHas('away', function(Builder $q) use($away_team) {
                                    $q->where('location', 'like', "%{$away_team}%")
                                        ->orWhere('nickname', 'like', "%{$away_team}%");
                                });
                            })
                            ->get();

            if($matches && count($matches) == 1) {
                $model = $matches[0];
                $model->ncaa_id = $ncaa_id;
                $model->save();
            }
        }

    }
}
