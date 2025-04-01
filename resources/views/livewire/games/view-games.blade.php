<div>

    <div class="flex items-center justify-center space-x-12 no-wrap mb-8 uppercase overflow-x-auto">

        <flux:button variant="filled" size="sm" wire:click="paginate('back')" icon="chevron-left" />

        @foreach ($this->dates as $date)
            @if ($date->calendar_date->toDateString() == $this->date)
                <div class="flex flex-col uppercase cursor-pointer border-b-2 border-blue-500 text-black dark:text-slate-200 text-center py-2 shrink-0">
                    <p class="text-xs font-semibold shrink-0">{{ $date->calendar_date->format('D') }}</p>
                    <p class="text-xs font-medium shrink-0">{{ $date->calendar_date->format('M d') }}</p>
                </div>
            @elseif ($date->calendar_type == 'offdays')
                <div class="shrink-0 text-gray-300 dark:text-gray-500">
                    <div class="flex flex-col items-center uppercase shrink-0">
                        <p class="text-xs shrink-0">{{ $date->calendar_date->format('D') }}</p>
                        <p class="text-xs font-light shrink-0">{{ $date->calendar_date->format('M d') }}</p>
                    </div>
                </div>
            @else
                <flux:button variant="ghost" wire:click="setDate('{{ $date->calendar_date->toDateString() }}')"
                    class="cursor-pointer shrink-0">
                    <div class="flex flex-col uppercase shrink-0">
                        <p class="text-xs shrink-0">{{ $date->calendar_date->format('D') }}</p>
                        <p class="text-xs font-light shrink-0">{{ $date->calendar_date->format('M d') }}</p>
                    </div>
                </flux:button>
            @endif
        @endforeach

        <flux:button variant="filled" size="sm" wire:click="paginate('forward')" icon="chevron-right" />

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        @foreach ($this->games as $game)
            <x-games.card :game="$game" />
        @endforeach

    </div>
</div>
