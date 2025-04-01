<?php

namespace App\Livewire\Games;

use Carbon\Carbon;
use App\Models\Game;
use Livewire\Component;
use App\Models\Calendar;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

#[Title('Scores')]
class ViewGames extends Component
{
    public $date;

    public ?Carbon $dp;

    public $dates;

    public $season;

    public function mount()
    {
        $this->season = config('espn.year');

        $this->date = Session::get('calendar-date', function () {

            return Calendar::where('season_id', $this->season)
                ->where('calendar_type', 'ondays')
                ->whereDate('calendar_date', '>=', today())
                ->min('calendar_date');
        });
    }

    public function render()
    {
        $this->dates = Calendar::where('season_id', $this->season)
            ->whereIn('calendar_type', ['ondays', 'offdays'])
            ->whereDate('calendar_date', '>=', Carbon::parse($this->date)->subDays(3)->format('Y-m-d'))
            ->orderBy('calendar_date')
            ->limit(7)
            ->get();

        return view('livewire.games.view-games');
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
        Session::put('calendar-date', $this->date);
    }

    public function paginate($direction)
    {

        $dt = Carbon::parse($this->date);

        $this->date = $direction == 'forward' ? $dt->addDays(7)->format('Y-m-d') : $dt->subDays(7)->format('Y-m-d');
    }
}
