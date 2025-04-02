<?php

namespace App\Livewire\Games;

use App\Http\Controllers\GameController;
use App\Jobs\Feeds\SyncPlays;
use App\Models\Game;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\On;

class ShowGame extends Component
{
    public $syncRate = 5; // Minutes between full game refreshes when live or behind

    public Game $game;

    public $situation;
    public $runners;

    #[On('echo:game.{game.id},.Plays')]
    public function newPlays($event)
    {
        $this->game = GameController::sync($this->game->id, 'live');
    }

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->runners = [];
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.games.show-game')->title($this->game->away->abbreviation.' @ '.$this->game->home->abbreviation);
    }

    public function refresh()
    {

        $lastSync = $this->game->updated_at->diffInMinutes(now());
        $sinceStart = $this->game->game_time->diffInMinutes(now());

        $liveOrBehind = $this->game->live || (! $this->game->final && $sinceStart > 0);

        // live or behind and due for round trip
        if ($liveOrBehind && $lastSync > $this->syncRate) {
            $this->game = GameController::sync($this->game->id, 'live');
        }

        if ($liveOrBehind) {

            // Rehydrate status
            if (isset($this->game->resources['status'])) {
                $status = Http::get($this->game->resources['status'])->json();
                unset($status['$ref']);

                $changed = $status['type']['shortDetail'] != $this->game->status['type']['shortDetail'];

                $this->game->status_id = $status['type']['id'];
                $this->game->status = $status;

                if($changed) {
                    $this->game->save();
                }

            }

            // Situation
            if ($liveOrBehind && isset($this->game->resources['situation'])) {
                $this->situation = Http::get($this->game->resources['situation'])->json();

                if(isset($this->situation['onFirst'])) array_push($this->runners, 'onFirst');
                if(isset($this->situation['onSecond'])) array_push($this->runners, 'onSecond');
                if(isset($this->situation['onThird'])) array_push($this->runners, 'onThird');

            }

            // Plays
            if ($liveOrBehind && isset($this->game->resources['plays'])) {
                SyncPlays::dispatch($this->game->id);
            }

        } elseif (($this->game->final || $this->game->cancelled) && !$this->game->finalized) {
            $this->game = GameController::sync($this->game->id, 'final');
        } else {
            // Game finalized, nothing to sync
        }
    }
}
