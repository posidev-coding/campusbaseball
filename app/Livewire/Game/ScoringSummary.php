<?php

namespace App\Livewire\Game;

use App\Models\Game;
use App\Models\Play;
use Livewire\Component;
use Livewire\Attributes\On;

class ScoringSummary extends Component
{
    public Game $game;
    public $plays;

    #[On('echo:game.{game.id},.Plays')] 
    public function newPlays($event)
    {
        $this->fetchPlays();
    }

    public function fetchPlays()
    {
        $this->plays = Play::where('game_id', $this->game->id)
                                ->where('scoring_play', 1)
                                ->orderBy('id')
                                ->get();
    }

}
