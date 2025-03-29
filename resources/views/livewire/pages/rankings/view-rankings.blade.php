<div>
    <flux:header>Rankings</flux:header>

    <flux:table >
        <flux:table.columns>
            <flux:table.column>Rank</flux:table.column>
            <flux:table.column>Team</flux:table.column>
            <flux:table.column>Record</flux:table.column>
            <flux:table.column>Previous</flux:table.column>
            <flux:table.column>Trend</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->rankings as $rank)
                <flux:table.row :key="$rank->current">

                    <flux:table.cell>{{ $rank->current }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center space-x-4">
                            <img src="{{ $rank->team->logos[0]['href'] }}" class="h-6 w-6" />
                            <div>{{ $rank->team->location }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $rank->team->record->summary ?? 'n/a' }}</flux:table.cell>
                    <flux:table.cell>{{ $rank->previous }}</flux:table.cell>
                    <flux:table.cell>{{ $rank->trend }}</flux:table.cell>
                
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>