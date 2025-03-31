<?php

namespace App\Livewire\Teams;

use App\Models\Conference;
use App\Models\Team;
use Livewire\Component;

class ViewTeams extends Component
{
    public $conference;

    public $conferences;

    public function mount()
    {
        $this->conferences = Conference::orderBy('name')->get();
    }

    public function setConf($id)
    {
        $this->conference = $id;
    }

    public function clearConf()
    {
        $this->conference = null;
    }

    #[\Livewire\Attributes\Computed]
    public function teams()
    {

        $teams = Team::has('conference')->with('liveHome', 'liveAway');

        // dd($teams->get()[0]);

        if ($this->conference) {
            $teams->where('conference_id', $this->conference);
        }

        return $teams->orderBy('display_name')->get();
    }
}
