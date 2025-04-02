<?php

namespace App\Livewire\Game;

use App\Models\Game;
use App\Models\Play;
use Livewire\Attributes\On;
use Livewire\Component;

class ScoringSummary extends Component
{
    public Game $game;
    public $loaded = false;
    public $plays;

    #[On('echo:game.{game.id},.Plays')]
    public function newPlays($event)
    {
        $this->fetchPlays();
        if(!$this->loaded) $this->loaded = true;
    }

    public function fetchPlays()
    {
        $this->plays = Play::where('game_id', $this->game->id)
            ->where('scoring_play', 1)
            ->orderBy('id')
            ->get();
    }
}
