<?php

namespace App\View\Components\Game;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BoxScore extends Component
{
    public $pitcher;

    public $batter;

    public function __construct(public Game $game, public mixed $situation) {}

    public function render(): View|Closure|string
    {
        return view('components.game.box-score');
    }

}
