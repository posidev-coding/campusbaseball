<?php

namespace App\Livewire\Teams;

use App\Models\Game;
use App\Models\Team;
use App\Models\Ranking;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;

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

        $this->tab = 'home';

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

    #[\Livewire\Attributes\Computed]
    public function games()
    {

        $games = Game::where('game_date', '<=', today())
            ->where('status_id', '!=', 1);

        $games->where(function(Builder $query) {
            $query->where('away_id', $this->team->id)
                    ->orWhere('home_id', $this->team->id);
        });

        $games = $games->orderBy('game_time', 'desc')->get();

        return $games;
    }

    #[\Livewire\Attributes\Computed]
    public function upcoming()
    {

        $games = Game::where('game_date', '>=', today())
            ->where('status_id', '!=', 3);

        $games->where(function(Builder $query) {
            $query->where('away_id', $this->team->id)
                    ->orWhere('home_id', $this->team->id);
        });

        $games = $games->orderBy('game_time', 'asc')->get();

        return $games;
    }

    #[\Livewire\Attributes\Computed]
    public function schedule()
    {

        $games = Game::where('away_id', $this->team->id)
                    ->orWhere('home_id', $this->team->id)
                    ->orderBy('game_time', 'asc')
                    ->get();

        return $games;
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
