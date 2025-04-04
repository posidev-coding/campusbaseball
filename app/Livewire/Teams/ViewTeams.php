<?php

namespace App\Livewire\Teams;

use App\Models\Conference;
use App\Models\Team;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Teams')]
class ViewTeams extends Component
{
    public $conference;

    public $conferences;

    public function mount()
    {
        $this->conferences = Conference::orderBy('short_name')->get();
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

        if ($this->conference) {
            $teams->where('conference_id', $this->conference);
        }

        return $teams->orderBy('display_name')->get();
    }
}
