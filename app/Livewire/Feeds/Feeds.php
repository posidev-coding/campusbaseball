<?php

namespace App\Livewire\Feeds;

use App\Jobs\Feeds\NCAAGames;
use App\Jobs\Feeds\SyncCalendar;
use App\Jobs\Feeds\SyncGames;
use App\Jobs\Feeds\SyncGroups;
use App\Jobs\Feeds\SyncRankings;
use App\Jobs\Feeds\SyncTeams;
use Flux\Flux;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Feeds')]
class Feeds extends Component
{
    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    #[\Livewire\Attributes\Computed]
    public function batches()
    {
        return DB::table('job_batches')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getBatches()
    {
        unset($this->batches);
    }

    public function cancel($id)
    {
        Bus::findBatch($id)->cancel();
        Flux::toast(variant: 'danger', text: 'Job cancelled');
    }

    public function run($job)
    {
        switch ($job) {
            case 'Teams':
                SyncTeams::dispatch();
                break;
            case 'Conferences':
                SyncGroups::dispatch();
                break;
            case 'Rankings':
                SyncRankings::dispatch();
                break;
            case 'Calendar':
                $batch = Bus::batch([new SyncCalendar])
                    ->name('Calendar')
                    ->dispatch();
                break;

            default:
                // code...
                break;
        }

        Flux::toast(variant: 'success', text: 'Dispatched the '.$job.' job');
    }

    public function games($scope)
    {

        if($scope == 'ncaa') {
            NCAAGames::dispatch();
        } else {
            SyncGames::dispatch($scope);
        }
        Flux::toast(variant: 'success', text: 'Dispatched game sync for '.$scope);
    }

    public function clear($scope)
    {
        switch ($scope) {
            case 'jobs':
                Artisan::call('queue:clear');
                break;
            case 'failed':
                Artisan::call('queue:prune-failed --hours=0');
                break;
            case 'finished':
                Artisan::call('queue:prune-batches --hours=0 --unfinished=0 --cancelled=0');
                break;
            default:
                // code...
                break;
        }

        Flux::toast(Artisan::output());
    }
}
