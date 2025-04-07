<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Models\Ranking;
use Livewire\Component;
use Illuminate\Support\Facades\Route;

class ShowTeam extends Component
{
    public Team $team;

    public bool $following;

    public int $rank;

    public string $route;

    public string $tab;

    public $articles;

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->articles = $team->articles();
        $this->route = Route::getCurrentRequest()->getRequestUri();
        $this->following = in_array($this->team->id, auth()->user()->teams ?? []);

        $this->tab = 'schedule';

        $week = Ranking::where('season_id', config('espn.year'))->max('week_nbr');

        $this->rank = Ranking::where('season_id', config('espn.year'))
            ->where('week_nbr', $week)
            ->where('team_id', $this->team->id)
            ->first()
            ->current ?? 0;

    }

    public function render()
    {
        return view('livewire.teams.show-team')->title($this->team->location.' '.$this->team->name)->layoutData([
            'icon' => $this->team->logo ?? null,
        ]);
    }

    public function toggle()
    {

        $user = auth()->user();

        $teams = $user->teams ?? [];

        if ($this->following) {
            $index = array_search($this->team->id, $teams);
            if ($index !== FALSE) {
                unset($teams[$index]);
            }
        } else {
            array_push($teams, $this->team->id);
        }

        $user->teams = array_unique($teams);
        $user->save();

        $this->following = (bool) !$this->following;

        return redirect()->to($this->route);

    }

}
