<?php

namespace App\Livewire\Pages\Games;

use App\Models\Game;
use Livewire\Component;

class ShowGame extends Component
{
    public Game $game;

    public function mount(Game $game)
    {
        $this->game = $game;
    }
}
