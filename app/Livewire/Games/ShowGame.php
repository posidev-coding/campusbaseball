<?php

namespace App\Livewire\Games;

use App\Http\Controllers\GameController;
use App\Jobs\Feeds\SyncPlays;
use App\Models\Game;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ShowGame extends Component
{
    public $syncRate = 5; // Minutes between full game refreshes when live or behind

    public Game $game;

    public $situation;

    public function mount(Game $game)
    {
        $this->game = $game;
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

                $this->game->status_id = $status['type']['id'];
                $this->game->status = $status;
            }

            // Situation
            if ($liveOrBehind && isset($this->game->resources['situation'])) {
                $this->situation = Http::get($this->game->resources['situation'])->json();
            }

            // Plays
            if ($liveOrBehind && isset($this->game->resources['plays'])) {
                SyncPlays::dispatch($this->game->id);
            }
        } elseif ($this->game->final) {

            // Game is final, do some cleanup
            if (isset($this->game->resources['plays']) && ! $this->game->play_cursor) {
                SyncPlays::dispatch($this->game->id);
            }

        }
    }
}
