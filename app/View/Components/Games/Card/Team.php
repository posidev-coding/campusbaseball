<?php

namespace App\View\Components\Games\Card;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Team extends Component
{
    public $runs;

    public $hits;

    public $errors;

    public $team;

    public $rank;

    public $record;

    public bool $winner;

    public function __construct(
        public $game,
        public ?bool $home,
        public ?bool $away
    ) {
        if ($home) {
            $this->team = $game->home;
            $this->winner = $game->home_winner;
            if ($game->home_rank > 0) {
                $this->rank = $game->home_rank;
            }
            if (isset($game->home_records) && count($game->home_records) > 0) {
                $this->record = $game->home_records[0]['summary'];
            }
            $this->runs = $game->home_runs;
            $this->hits = $game->home_hits;
            $this->errors = $game->home_errors;
        } else {
            $this->team = $game->away;
            $this->winner = $game->away_winner;
            if ($game->away_rank > 0) {
                $this->rank = $game->away_rank;
            }
            if (isset($game->away_records) && count($game->away_records) > 0) {
                $this->record = $game->away_records[0]['summary'];
            }
            $this->runs = $game->away_runs;
            $this->hits = $game->away_hits;
            $this->errors = $game->away_errors;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.games.card.team');
    }
}
