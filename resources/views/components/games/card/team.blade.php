<div class="flex items-center justify-between">
    <div class="flex items-center space-x-4">
        @isset($team->logo)
            <x-game.team-logo :team="$team" size="8" />
        @endisset
        <div class="flex flex-col">
            <div class="flex flex-row items-center">
                @if ($rank)
                    <div class="text-gray-400 dark:text-gray-400 text-xs mr-1">{{ $rank }}</div>
                @endif
                <div @class([
                    'text-base',
                    'font-medium',
                    'text-black dark:text-gray-100',
                    'text-gray-500 dark:text-gray-400' => $game->status_id == 3 && !$winner,
                    'font-normal' => $game->status_id == 3 && !$winner,
                ])>{{ $team->location ?? 'N/A' }}</div>
            </div>
            @if ($record)
                <div class="text-gray-400 dark:text-gray-300 tracking-wide text-[10px] font-light">({{ $record }})</div>
            @endif
        </div>
    </div>
    @if ($game->status_id > 1)
        <div class="flex">
            <flux:text @class([
                'w-[40px]',
                'text-center',
                'text-black dark:text-gray-100' => !in_array($game->status_id, [1, 3]) || $winner,
                'font-medium' => !in_array($game->status_id, [1, 3]) || $winner,
            ])>{{ $runs }}</flux:text>
            <flux:text class="w-[40px] text-center">{{ $hits }}</flux:text>
            <flux:text class="w-[40px] text-center">{{ $errors }}</flux:text>
        </div>
    @endif
</div>
