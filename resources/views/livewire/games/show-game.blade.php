<div wire:poll.30s="refresh">

    <x-game.matchup :game="$game" />
    <x-game.box-score :game="$game" :situation="$situation" />

    <!-- Three column body layout for components -->
    <div class="flex flex-col md:flex-row mt-4 gap-x-4 lg:gap-x-6">

        <!-- Left on desktop, 2nd on mobile -->
        <div class="order-2 md:order-1 w-full md:w-1/4 flex flex-col gap-2.5 lg:gap-4 mt-4 lg:mt-0">
            <x-game.venue :game="$game" />
        </div>

        <!-- Center on desktop, 1st on mobile -->
        <div class="order-1 md:order-2 w-full md:w-1/2 flex flex-col gap-2.5 lg:gap-4">
            @if ($game->status_id > 1 && isset($game->resources['plays']))
                <livewire:game.gamecast :game="$game" />
            @else
                <x-skeleton.list />
            @endif
        </div>

        <!-- Right on desktop, 3rd on mobile -->
        <div class="order-3 md:order-3 w-full md:w-1/4 flex flex-col gap-2.5 lg:gap-4 mt-4 lg:mt-0">
            <x-skeleton.list />
        </div>

    </div>

</div>
