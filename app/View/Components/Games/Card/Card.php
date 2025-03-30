<?php

namespace App\View\Components\Games\Card;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(public Game $game) {}

    public function render(): View|Closure|string
    {
        return view('components.games.card.card');
    }
}
