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

    public $tab = 'scoring';

    #[On('echo:game.{game.id}.Plays')]
    public function newPlays($event)
    {
        $this->fetchPlays();
        if (! $this->loaded) {
            $this->loaded = true;
        }
    }

    public function render()
    {

        $query = Play::where('game_id', $this->game->id);

        if($this->tab == 'scoring') {
            $query->where('scoring_play', 1);
        }

        $this->plays = $query->orderBy('id')->get();

        // dd($this->plays);
        
        return view('livewire.game.scoring-summary');
    }
}
