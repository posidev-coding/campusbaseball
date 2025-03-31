<?php

namespace App\View\Components\Game;

use App\Models\Game;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BoxScore extends Component
{
    public $innings;

    public $final;

    public function __construct(public Game $game, public mixed $situation) {}

    public function render(): View|Closure|string
    {
        return view('components.game.box-score', [
            'innings' => $this->innings(),
        ]);
    }

    public function innings()
    {
        $boxes = count($this->game->away_box);
        $frames = $boxes > 0 ? ($boxes > 9 ? $boxes : 9) : 0;

        $innings = [];

        for ($i = 0; $i < $frames; $i++) {

            $away = $this->game->away_box[$i] ?? [
                'inning' => ($i + 1),
                'runs' => '-',
                'hits' => '-',
                'errors' => '-',
            ];
            $home = $this->game->home_box[$i] ?? [
                'inning' => ($i + 1),
                'runs' => '-',
                'hits' => '-',
                'errors' => '-',
            ];

            array_push($innings, [
                'away' => $away,
                'home' => $home,
            ]);
        }

        return $innings;

    }
}
