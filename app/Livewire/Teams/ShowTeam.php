<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;

class ShowTeam extends Component
{
    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function render()
    {
        return view('livewire.teams.show-team')->title($this->team->location.' '.$this->team->name)->layoutData([
            'icon' => $this->team->logos[0]['href'] ?? null,
        ]);
    }
}
