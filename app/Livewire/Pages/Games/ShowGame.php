<?php

namespace App\Livewire\Pages\Games;

use App\Http\Controllers\GameController;
use App\Models\Game;
use Livewire\Component;

class ShowGame extends Component
{
    public Game $game;

    public $situation;

    public $syncRate = 1; // Minutes between game refreshes

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->refresh();
    }

    public function refresh()
    {

        $lastSync = $this->game->updated_at->diffInMinutes(now());
        $sinceStart = $this->game->game_time->diffInMinutes(now());

        // more than five minutes and live or behind
        if ($lastSync > $this->syncRate && ($this->game->live || (! $this->game->completed && $sinceStart > 0))) {
            $this->game = GameController::sync($this->game->id, 'live');
        }
    }
}
