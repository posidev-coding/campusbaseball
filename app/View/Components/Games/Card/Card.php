<?php

namespace App\View\Components\Games\Card;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Game $game) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.games.card.card');
    }
}
