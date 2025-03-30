<?php

namespace App\Jobs\Feeds;

use App\Models\Game;
use App\Models\Play;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use function PHPUnit\Framework\assertFalse;

class SyncGamePlays implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    private Game $game;

    private int $playCursor;
    private int $pageCursor;
    private int $playCount;

    public function __construct(int $game, $all = false)
    {
        $this->game = Game::find($game);
        $this->pageCursor = $all ? 1 : ($this->game->play_page ?? 1);
        $this->playCursor = $this->game->play_cursor ?? 0;
        $this->playCount = 0;
    }

    public function handle(): void
    {
        
        $this->paginate();
        
        if($this->playCursor > 0) {
            $this->game->play_page = $this->pageCursor;
            $this->game->play_cursor = $this->playCursor;
            $this->game->save();
        }

    }

    // recursive
    public function paginate()
    {

        $data = Http::get($this->game->resources['plays'] . '&limit=25&page='.$this->pageCursor)->json();

        foreach($data['items'] as $play) {

            // only upsert plays after the game cursor
            if($play['id'] > $this->game->play_cursor) {
                $model = Play::updateOrCreate(
                    [
                        'id' => $play['id']
                    ],
                    [
                        'game_id' => $this->game->id,
                        'sequence' => intval($play['sequenceNumber']),
                        'inning' => $play['period']['number'],
                        'type_id' => $play['type']['id'],
                        'type_text' => $play['type']['text'],
                        'text' => $play['text'] ?? '(Type) ' . $play['type']['text']
                    ]
                );
                $this->playCount ++;
            }

            // only advance cursor once it has been stored
            $this->playCursor = $play['id'];
            
        }
        
        if($data['pageIndex'] < $data['pageCount']) {
            $this->pageCursor++;
            $this->paginate();
        } else {
            return true;
        }

    }

}
