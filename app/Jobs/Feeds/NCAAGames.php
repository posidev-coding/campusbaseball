<?php

namespace App\Jobs\Feeds;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class NCAAGames implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Queueable;

    public $url;
    public array $jobs;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->url = config('ncaa.games');
        $this->jobs = [];
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $this->paginate();

        Bus::batch($this->jobs)
            ->name('NCAA Games')
            ->dispatch();

    }

    public function paginate()
    {

        if($data = Http::get($this->url)->json()) {
            foreach($data['data'] as $game) {
                array_push($this->jobs, new NCAAGame($game['id']));
            }
    
            if(isset($data['meta']['pagination']['next_page']) && !is_null($data['meta']['pagination']['next_page'])) {
                $this->url = config('ncaa.games') . '&page=' . $data['meta']['pagination']['next_page'];
                $this->paginate();
            }
        }

    }
}
