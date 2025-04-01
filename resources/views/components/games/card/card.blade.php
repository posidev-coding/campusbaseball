<a href="{{ route('game', $game->id) }}"
    class="flex flex-col bg-card border border-gray-150 dark:border-muted rounded-lg p-2 cursor-pointer hover:border-gray-400">

    <!-- Status & Box Headers -->
    <div class="flex items-center justify-between mb-1">
        <div @class([
            'text-[11px]',
            'font-medium',
            'uppercase',
            'tracking-wider',
            'text-black dark:text-slate-300' => in_array($game->status_id, [1, 3]),
            'text-live' => !in_array($game->status_id, [1, 3]),
        ])>
            {{ $game->status['type']['shortDetail'] }}
        </div>
        @if ($game->status_id > 1)
            <div class="flex">
                <flux:text class="w-[40px] uppercase text-center text-xs text-black dark:text-slate-300">R</flux:text>
                <flux:text class="w-[40px] uppercase text-center text-xs text-black dark:text-slate-300">H</flux:text>
                <flux:text class="w-[40px] uppercase text-center text-xs text-black dark:text-slate-300">E</flux:text>
            </div>
        @endif
    </div>

    <x-games.card.team :game="$game" away />
    <x-games.card.team :game="$game" home />

</a>
