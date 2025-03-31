<?php

namespace App\Livewire\Rankings;

use App\Models\Ranking;
use Livewire\Component;

class ViewRankings extends Component
{
    public int $week;

    public function mount()
    {
        $this->week = Ranking::where('season_id', config('espn.year'))->max('week_nbr');
        // dd($this->week);2
    }

    #[\Livewire\Attributes\Computed]
    public function rankings()
    {
        return Ranking::where('week_nbr', $this->week)->orderBy('current')->get();
    }
}
