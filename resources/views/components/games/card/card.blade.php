<a href="{{ route('game', $game->id) }}"
    class="flex flex-col bg-white border border-gray-150 rounded-lg p-2 cursor-pointer hover:border-gray-400">

    <!-- Status & Box Headers -->
    <div class="flex items-center justify-between mb-1">
        <div @class([
            'text-[10px]',
            'font-semibold',
            'uppercase',
            'text-black' => in_array($game->status_id, [1, 3]),
            'text-red-600' => !in_array($game->status_id, [1, 3]),
        ])>
            {{ $game->status['type']['shortDetail'] }}
        </div>
        @if ($game->status_id > 1)
            <div class="flex">
                <flux:text class="w-[40px] uppercase text-center text-xs text-black">R</flux:text>
                <flux:text class="w-[40px] uppercase text-center text-xs text-black">H</flux:text>
                <flux:text class="w-[40px] uppercase text-center text-xs text-black">E</flux:text>
            </div>
        @endif
    </div>

    <x-games.card.team :game="$game" away />
    <x-games.card.team :game="$game" home />

</a>
