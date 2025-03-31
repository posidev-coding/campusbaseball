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
}
