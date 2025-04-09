<div>

    <flux:table :paginate="$this->teams">

        <flux:table.columns class="bg-card-header">
            <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')">ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'location'" :direction="$sortDirection" wire:click="sort('location')">Location</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'short_display_name'" :direction="$sortDirection" wire:click="sort('short_display_name')">Short Name</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>

            @foreach ($this->teams as $team)
                <flux:table.row :key="$team->id">
                    <flux:table.cell>{{ $team->id }}</flux:table.cell>
                    <flux:table.cell>{{ $team->location }}</flux:table.cell>
                    <flux:table.cell>{{ $team->name }}</flux:table.cell>
                    <flux:table.cell>{{ $team->short_display_name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="xs" wire:click="match({{ $team->id }})">Match</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach

        </flux:table.rows>

    </flux:table>

    <flux:modal name="matcher" class="md:w-96">
        @if($this->team)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Match Team</flux:heading>
                    <flux:text class="mt-2">{{ $this->team->location . ' ' . $this->team->name }}</flux:text>
                </div>
                
                {{-- <flux:input label="Search" wire:model="search" placeholder="Search NCAA Teams" /> --}}
                <flux:select variant="listbox" wire:model="assignment" searchable placeholder="Assign Team...">
                    @foreach($options as $option)    
                        <flux:select.option :value="$option->id">{{ $option->short_name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <div class="flex">
                    <flux:spacer />
                    <flux:button wire:click="assign" variant="primary">Save</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

</div>
