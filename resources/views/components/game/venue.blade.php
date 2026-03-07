@props(['game'])
<div class="flex flex-col overflow-hidden border dark:border-muted rounded-lg">

    @if (isset($game->venue['images'][1]) || isset($game->venue['images'][0]))
        <div class="flex-shrink-0">
            <img class="w-full object-cover object-top h-40"
                src="{{ isset($game->venue['images'][1]) ? $game->venue['images'][1]['href'] : $game->venue['images'][0]['href'] }}"
                alt="">
        </div>
    @endif

    <div class="flex flex-col flex-1 px-4 py-2">

        <h2 class="text-base font-bold">
            {{ $game->venue['fullName'] }}
        </h2>

        @if (isset($game->venue['address']['city']))
            <div class="flex flex-row items-center space-x-2 mt-2.5">
                <img src="{{ secure_asset('images/location.svg') }}" class="h-4" />
                <div class="text-sm font-normal text-gray-700 dark:text-gray-200">
                    {{ $game->venue['address']['city'] }}
                    @if (isset($game->venue['address']['state']))
                        {{ ', ' . $game->venue['address']['state'] }}
                    @endif
                </div>
            </div>
        @endif

        <div class="flex flex-row items-center mt-1.5">
            @if (isset($game->venue['capacity']))
                <div class="flex flex-row items-center space-x-2 w-1/2">
                    <img src="{{ secure_asset('images/fans.svg') }}" class="h-4" />\
                    <div class="text-sm font-normal text-gray-700 dark:text-gray-200">
                        {{ number_format($game->venue['capacity']) }}
                    </div>
                </div>
            @endif
            <div class="flex flex-row items-center space-x-2 w-1/2">
                <img src="{{ secure_asset('images/grass.svg') }}" class="h-4" />
                <div class="text-sm font-normal text-gray-700 dark:text-gray-200">
                    {{ $game->venue['grass'] ? 'Grass' : 'Artificial' }}
                </div>
            </div>
        </div>
    </div>
</div>
