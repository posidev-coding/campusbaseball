<?php

namespace App\Livewire\Rankings;

use App\Models\Ranking;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Rankings')]
class ViewRankings extends Component
{
    public int $week;

    public function mount()
    {
        $this->week = Ranking::where('season_id', config('espn.year'))->max('week_nbr');
    }

    #[\Livewire\Attributes\Computed]
    public function rankings()
    {
        return Ranking::where('season_id', config('espn.year'))->where('week_nbr', $this->week)->orderBy('current')->get();
    }
}
