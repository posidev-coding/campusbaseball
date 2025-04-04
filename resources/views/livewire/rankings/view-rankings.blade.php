<div class="flex flex-col bg-card border dark:border-muted rounded-lg lg:mx-4 max-w-4xl grow cursor-pointer hover:border-gray-400">
    {{-- <flux:header size="sm">D1Baseball.com Poll</flux:header> --}}

    <flux:table >
        <flux:table.columns class="bg-card-header">
            <flux:table.column><p class="pl-6 md:pl-8">Rank</p></flux:table.column>
            <flux:table.column>Team</flux:table.column>
            <flux:table.column>Record</flux:table.column>
            <flux:table.column>Previous</flux:table.column>
            <flux:table.column><p class="pr-6 md:pr-8">Trend</p></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->rankings as $rank)
                <flux:table.row :key="$rank->current">

                    <flux:table.cell><p class="pl-6 md:pl-8">{{ $rank->current }}</p></flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center space-x-4">
                            <div clas="flex">
                                <img src="{{ $rank->team->logo }}" alt="{{ $rank->team->abbreviation }}" class="w-6 h-6 flex dark:hidden" />
                                <img src="{{ $rank->team->darkLogo }}" alt="{{ $rank->team->abbreviation }}" class="w-6 h-6 hidden dark:flex" />
                            </div>
                            <div>{{ $rank->team->location }}</div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $rank->team->record->summary ?? 'n/a' }}</flux:table.cell>
                    <flux:table.cell>{{ $rank->previous }}</flux:table.cell>
                    <flux:table.cell><p class="pr-6 md:pr-8">{{ $rank->trend }}</p></flux:table.cell>
                
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>