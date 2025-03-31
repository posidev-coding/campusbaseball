<?php

namespace App\Livewire\Games;

use App\Models\Calendar;
use App\Models\Game;
use Carbon\Carbon;
use Livewire\Component;

class ViewGames extends Component
{
    public $date;

    public ?Carbon $dp;

    public $dates;

    public $season;

    public function mount()
    {
        $this->season = config('espn.year');
        $this->date = today()->format('Y-m-d');
    }

    public function render()
    {
        $this->dates = Calendar::where('season_id', $this->season)
            ->whereIn('calendar_type', ['ondays', 'offdays'])
            ->whereDate('calendar_date', '>=', Carbon::parse($this->date)->subDays(3)->format('Y-m-d'))
            ->orderBy('calendar_date')
            ->limit(7)
            ->get();

        return view('livewire.pages.games.view-games');
    }

    #[\Livewire\Attributes\Computed]
    public function games()
    {

        $live = Game::where('game_date', $this->date)
            ->whereNotIn('status_id', [1, 3])
            ->orderBy('status_id', 'ASC')
            ->get();

        $other = Game::where('game_date', $this->date)
            ->whereIn('status_id', [1, 3])
            ->orderBy('status_id', 'ASC')
            ->get();

        return $live->merge($other);
    }

    public function setDate($dt)
    {
        $this->date = $dt;
    }

    public function paginate($direction)
    {

        $dt = Carbon::parse($this->date);

        $this->date = $direction == 'forward' ? $dt->addDays(7)->format('Y-m-d') : $dt->subDays(7)->format('Y-m-d');
    }
}
