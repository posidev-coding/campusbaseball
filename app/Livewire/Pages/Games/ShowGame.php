<?php

namespace App\Livewire\Pages\Games;

use App\Models\Game;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\GameController;

class ShowGame extends Component
{
    public $syncRate = 2; // Minutes between full game refreshes when live or behind

    public Game $game;
    public $situation;

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->refresh();
    }

    public function refresh()
    {

        $lastSync = $this->game->updated_at->diffInMinutes(now());
        $sinceStart = $this->game->game_time->diffInMinutes(now());

        $liveOrBehind = $this->game->live || (! $this->game->completed && $sinceStart > 0);

        // live or behind and due for round trip
        if ($liveOrBehind && $lastSync > $this->syncRate) {
            $this->game = GameController::sync($this->game->id, 'live');
        } else if($liveOrBehind && isset($this->game->resources['situation'])) {

            // Rehydrate status
            $status = Http::get($this->game->resources['status'])->json();
            unset($status['$ref']);

            $this->game->status_id = $status['type']['id'];
            $this->game->status = $status;
        }

        if($liveOrBehind && isset($this->game->resources['situation'])) {
            $this->situation = Http::get($this->game->resources['situation'])->json();
        }

    }

}
