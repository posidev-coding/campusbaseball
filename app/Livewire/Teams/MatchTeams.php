<?php

namespace App\Livewire\Teams;

use Flux\Flux;
use App\Models\Team;
use Livewire\Component;
use App\Models\NCAATeam;
use Livewire\WithPagination;

class MatchTeams extends Component
{

    use WithPagination;

    public $team;

    public $search;

    public $results;

    public $assignment;

    public $options;

    public $sortBy = 'location';
    public $sortDirection = 'asc';
    
    public function sort($column) {
        if($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $this->options = NCAATeam::orderBy('short_name')->get();
        return view('livewire.teams.match-teams');
    }

    #[\Livewire\Attributes\Computed]
    public function teams()
    {
        return Team::whereNull('ncaa_id')->orderBy('display_name')->paginate(10);
    }

    public function match($id)
    {
        $this->team = Team::find($id);
        Flux::modal('matcher')->show();
    }

    public function assign()
    {

        $this->team->ncaa_id = $this->assignment;
        $this->team->save();

        $this->assignment = null;
        $this->team = null;

        Flux::modal('matcher')->close();
        Flux::toast(variant: 'success', text: 'Saved team!');
        
    }

}
