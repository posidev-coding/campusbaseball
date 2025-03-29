<div class="flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <img src="{{ $team->logos[0]['href'] }}" class="h-8 w-8" />
        <div class="flex flex-col">
            <div class="flex flex-row items-center">
                @if ($rank)
                    <div class="text-gray-400 text-xs mr-1">{{ $rank }}</div>
                @endif
                <div @class([
                    'text-base',
                    'font-medium',
                    'text-black',
                    'text-gray-400' => $game->status_id == 3 && !$winner,
                    'font-normal' => $game->status_id == 3 && !$winner,
                ])>{{ $team->location }}</div>
            </div>
            @if ($record)
                <div class="text-gray-400 text-[10px] font-light">({{ $record }})</div>
            @endif
        </div>
    </div>
    @if ($game->status_id > 1)
        <div class="flex">
            <flux:text @class([
                'w-[40px]',
                'text-center',
                'text-black' => !in_array($game->status_id, [1, 3]) || $winner,
                'font-medium' => !in_array($game->status_id, [1, 3]) || $winner,
            ])>{{ $runs }}</flux:text>
            <flux:text class="w-[40px] text-center">{{ $hits }}</flux:text>
            <flux:text class="w-[40px] text-center">{{ $errors }}</flux:text>
        </div>
    @endif
</div>
