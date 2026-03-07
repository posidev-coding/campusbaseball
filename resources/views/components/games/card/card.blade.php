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
            @isset($game->status['type']['shortDetail'])
                {{ $game->status['type']['shortDetail'] }}
            @else
                {{ $game->id }}
            @endisset
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

    <div class="px-2.5 pt-1 flex flex-row items-center justify-between gap-x-2">
        @if(isset($game->broadcasts[0]['station']) && !$game->final && !$game->cancelled && !$game->suspended)
            <div class="flex text-xs text-gray-500">
                {{ $game->broadcasts[0]['station'] }}
            </div>
        @endif
        <div class="flex flex-row items-center justify-end gap-x-2">
            @if($game->watch_espn && $game->live)
                <flux:badge as="button" color="blue" variant="solid" size="sm">Watch</flux:badge>
            @endif
            @if($game->gamecast_available)
                <flux:badge size="sm">Gamecast</flux:badge>
            @endif
            @if($game->boxscore_available)
                <flux:badge size="sm">Box Score</flux:badge>
            @endif
            @if($game->pbp_available)
                <flux:badge size="sm">Play-By-Play</flux:badge>
            @endif
        </div>
    </div>

</a>
