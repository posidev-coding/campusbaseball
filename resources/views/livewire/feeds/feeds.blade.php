<div wire:poll.5s="getBatches">

    <div class="flex items-center space-x-12 mb-8">
        <div class="text-2xl font-semibold">Data Feeds</div>
        <flux:dropdown position="right">
            <flux:button variant="primary" icon:trailing="chevron-down">Batch Jobs</flux:button>
            <flux:menu>

                <flux:menu.item icon="flag" icon:variant="outline" wire:click="run('Teams')" class="cursor-pointer">Teams
                </flux:menu.item>
                <flux:menu.item icon="rectangle-group" icon:variant="outline" wire:click="run('Conferences')"
                    class="cursor-pointer">Conferences</flux:menu.item>
                <flux:menu.item icon="hashtag" icon:variant="outline" wire:click="run('Rankings')"
                    class="cursor-pointer">Rankings</flux:menu.item>
                <flux:menu.item icon="calendar" icon:variant="outline" wire:click="run('Calendar')"
                    class="cursor-pointer">Calendar</flux:menu.item>

                <flux:menu.separator />

                <flux:menu.submenu icon="table-properties" icon:variant="outline" heading="Games">
                    <flux:menu.item wire:click="games('live')" class="cursor-pointer">Live</flux:menu.item>
                    <flux:menu.item wire:click="games('today')" class="cursor-pointer">Today</flux:menu.item>
                    <flux:menu.item wire:click="games('tomorrow')" class="cursor-pointer">Tomorrow</flux:menu.item>
                    <flux:menu.item wire:click="games('yesterday')" class="cursor-pointer">Yesterday</flux:menu.item>
                    <flux:menu.item wire:click="games('future')" class="cursor-pointer">All Future</flux:menu.item>
                    <flux:menu.item wire:click="games('past')" class="cursor-pointer">All Past</flux:menu.item>
                    <flux:menu.item wire:click="games('full')" class="cursor-pointer">Full Calendar</flux:menu.item>
                    </flux:menu.group>

                    <flux:menu.separator />

                    <flux:menu.submenu icon="trash" icon:variant="outline" heading="Cleanup">
                        <flux:menu.item wire:click="clear('jobs')" class="cursor-pointer">Clear Job Queue
                        </flux:menu.item>
                        <flux:menu.item wire:click="clear('failed')" class="cursor-pointer">Clear Failed Jobs
                        </flux:menu.item>
                        <flux:menu.item wire:click="clear('finished')" class="cursor-pointer">Clear Finished Batches
                        </flux:menu.item>
                    </flux:menu.submenu>

            </flux:menu>
        </flux:dropdown>
    </div>

    <flux:table :paginate="$this->batches">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'total_jobs'" :direction="$sortDirection"
                wire:click="sort('total_jobs')">Total Jobs</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'pending_jobs'" :direction="$sortDirection"
                wire:click="sort('pending_jobs')">Pending</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'failed_jobs'" :direction="$sortDirection"
                wire:click="sort('failed_jobs')">Failed</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Start</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'finished_at'" :direction="$sortDirection"
                wire:click="sort('finished_at')">Duration</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->batches as $batch)
                <flux:table.row :key="$batch->id">

                    <flux:table.cell>{{ $batch->name }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="slate">{{ $batch->total_jobs }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="slate">{{ $batch->pending_jobs }}</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" color="slate">{{ $batch->failed_jobs }}</flux:badge>
                    </flux:table.cell>

                    @if ($batch->finished_at && $batch->failed_jobs > 0)
                        <flux:table.cell>
                            <flux:badge size="sm" color="red">Failed</flux:badge>
                        </flux:table.cell>
                    @elseif ($batch->cancelled_at)
                        <flux:table.cell>
                            <flux:badge size="sm" color="amber">Cancelled</flux:badge>
                        </flux:table.cell>
                    @elseif ($batch->finished_at && $batch->failed_jobs == 0)
                        <flux:table.cell>
                            <flux:badge size="sm" color="green">Successful</flux:badge>
                        </flux:table.cell>
                    @elseif (!$batch->finished_at)
                        <flux:table.cell>
                            <flux:badge size="sm" color="blue">Running..</flux:badge>
                        </flux:table.cell>
                    @else
                        <flux:table.cell>Other</flux:table.cell>
                    @endif

                    <flux:table.cell>
                        {{ \Carbon\Carbon::parse($batch->created_at)->setTimezone('America/New_York')->format('n/j g:i:s A') }}
                    </flux:table.cell>

                    @if (!$batch->cancelled_at && !$batch->finished_at)
                        <flux:table.cell>
                            <flux:button size="xs" icon="loading" tooltip="Cancel batch"
                                wire:click="cancel('{{ $batch->id }}')" class="cursor-pointer">Cancel</flux:button>
                        </flux:table.cell>
                    @else
                        <flux:table.cell>
                            {{ \Carbon\Carbon::parse($batch->cancelled_at ?? $batch->finished_at)->diff(\Carbon\Carbon::parse($batch->created_at)) }}
                        </flux:table.cell>
                    @endif

                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

</div>
