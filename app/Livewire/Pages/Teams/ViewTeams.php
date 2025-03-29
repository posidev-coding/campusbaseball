<?php

namespace App\Livewire\Pages\Teams;

use App\Models\Team;
use Livewire\Component;

class ViewTeams extends Component
{
    #[\Livewire\Attributes\Computed]
    public function teams()
    {

        return Team::orderBy('location')->get();
    }
}
