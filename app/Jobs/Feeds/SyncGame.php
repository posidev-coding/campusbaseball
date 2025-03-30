<?php

namespace App\Jobs\Feeds;

use App\Http\Controllers\GameController;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SyncGame implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 3;

    private $game;

    private $scope;

    public function __construct(int $game, $scope = 'live')
    {
        $this->game = $game;
        $this->scope = $scope;
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function handle(): void
    {
        GameController::sync($this->game, $this->scope);
    }
}
