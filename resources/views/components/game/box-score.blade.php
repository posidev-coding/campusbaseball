<div class="flex justify-center">

    <div @class([
        'border dark:border-muted rounded-lg mt-2 md:mt-4 lg:mx-4 grow',
        'max-w-5xl' => count($this->game->away_box) > 12,
        'max-w-4xl' => count($this->game->away_box) > 0,
        'max-w-sm' => count($this->game->away_box) == 0,
    ])>
        <div class="flex items-stretch w-full">
            <div class="flex flex-col">
                <div class="bg-card-header flex text-muted text-xs items-center justify-center h-7 border-b dark:border-b-muted rounded-tl-lg">
                    @if (!$game->final && isset($game->broadcasts[0]['station']))
                        <span>{{ $game->broadcasts[0]['station'] }}</span>
                    @endif
                </div>
                <div class="flex items-center px-2 h-7 bg-lighter/10 dark:bg-card border-b dark:border-b-muted">
                    @isset($this->game->away->logo)
                        <x-game.team-logo :team="$game->away" size="5" />
                    @endisset
                    <div @class([
                        'hidden sm:flex ml-2',
                        'text-sm font-light',
                        'text-winner font-medium' => $this->game->away_winner,
                        'text-loser' => !$this->game->away_winner,
                    ])>{{ $this->game->away->abbreviation ?? 'N/A' }}</div>
                </div>
                <div class="flex items-center px-2 h-7 bg-lighter/10 dark:bg-card rounded-bl-lg">
                    @isset($this->game->home->logo)
                        <x-game.team-logo :team="$this->game->home" size="5" />
                    @endisset
                    <div @class([
                        'hidden sm:flex ml-2',
                        'text-sm font-light',
                        'text-winner font-medium' => $this->game->home_winner,
                        'text-loser' => !$this->game->home_winner,
                    ])>{{ $this->game->home->abbreviation ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="flex grow">

                @foreach ($innings as $inning)
                    <div class="flex flex-col grow border-l dark:border-l-muted text-sm font-light">
                        <div @class([
                            'h-7 place-content-center text-center text-gray-500 dark:text-white bg-card-header border-b dark:border-b-muted',
                            'text-gray-900 font-semibold' =>
                                !$this->game->final &&
                                $inning['away']['inning'] == $this->game->status['period'],
                        ])>{{ $inning['away']['inning'] }}</div>
                        <div @class([
                            'h-7 place-content-center text-center text-gray-700 dark:text-light bg-card border-b dark:border-b-muted',
                            'bg-blue-50' =>
                                !$this->game->final &&
                                $inning['away']['inning'] == $this->game->status['period'] &&
                                $this->game->status['periodPrefix'] == 'Top',
                        ])>{{ $inning['away']['runs'] }}</div>
                        <div @class([
                            'h-7 place-content-center text-center text-gray-700 dark:text-light bg-card',
                            'bg-blue-50' =>
                                !$this->game->final &&
                                ($box['inning'] ?? null) == $this->game->status['period'] &&
                                $this->game->status['periodPrefix'] == 'Bottom',
                        ])>{{ $inning['home']['runs'] }}</div>
                    </div>
                @endforeach

                <div class="flex flex-col grow border-l dark:border-l-muted font-semibold dark:font-medium text-sm">
                    <div
                        class="h-7 place-content-center text-center text-gray-500 dark:text-white bg-card-header border-b dark:border-b-muted">
                        R</div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-gray-200 border-b dark:border-b-muted bg-card dark:bg-darker">
                        {{ $this->game->away_runs ?? '-' }}
                    </div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-white bg-card dark:bg-darker">
                        {{ $this->game->home_runs ?? '-' }}
                    </div>
                </div>
                <div class="flex flex-col grow border-l dark:border-l-muted font-semibold dark:font-medium text-sm">
                    <div
                        class="h-7 place-content-center text-center text-gray-500 dark:text-white bg-card-header border-b dark:border-b-muted">
                        H</div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-white border-b dark:border-b-muted bg-card dark:bg-darker">
                        {{ $this->game->away_hits ?? '-' }}
                    </div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-white bg-card dark:bg-darker">
                        {{ $this->game->home_hits ?? '-' }}
                    </div>
                </div>
                <div class="flex flex-col grow border-l dark:border-l-muted font-semibold dark:font-medium text-sm">
                    <div
                        class="h-7 place-content-center text-center text-gray-500 dark:text-white bg-card-header border-b dark:border-b-muted rounded-tr-lg">
                        E</div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-white border-b dark:border-b-muted bg-card dark:bg-darker">
                        {{ $this->game->away_errors ?? '-' }}
                    </div>
                    <div
                        class="h-7 place-content-center text-center text-gray-700 dark:text-white bg-card dark:bg-darker rounded-br-lg">
                        {{ $this->game->home_errors ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        @if (isset($this->situation['pitcher']))

            @php
                $pitcher = isset($this->situation['pitcher']['athlete']['$ref'])
                    ? Http::get($this->situation['pitcher']['athlete']['$ref'])->json()
                    : null;
                $pitcherStats = isset($this->situation['pitcher']['statistics']['$ref'])
                    ? Http::get($this->situation['pitcher']['statistics']['$ref'])->json()
                    : null;
                $batter = isset($this->situation['batter']['athlete']['$ref'])
                    ? Http::get($this->situation['batter']['athlete']['$ref'])->json()
                    : null;
                $batterStats = isset($this->situation['batter']['statistics']['$ref'])
                    ? Http::get($this->situation['batter']['statistics']['$ref'])->json()
                    : null;
                $lastPlay = isset($this->situation['lastPlay']['$ref'])
                    ? Http::get($this->situation['lastPlay']['$ref'])->json()
                    : null;
            @endphp

            <div class="border-t p-2 flex items-center justify-center space-x-12">

                <!-- Pitcher -->
                <div class="flex items-center space-x-4">
                    <div>
                        @if ($this->game->status['periodPrefix'] == 'Top')
                            <!-- Home team is pitching -->
                            <img src="{{ $this->game->home->logo }}" class="h-10 w-10" />
                        @else
                            <!-- Away team is pitching -->
                            <img src="{{ $this->game->away->logo }}" class="h-10 w-10" />
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="text-xs font-semibold">PITCHER</div>
                        <div class="text-sm font-light tracking-wide">{{ $pitcher['shortName'] }}</div>
                        <div class="text-xs text-gray-400">
                            @foreach ($pitcherStats['splits']['categories'] as $stat)
                                @if ($stat['name'] == 'pitching')
                                    {{ $stat['summary'] }}
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Batter -->
                <div class="flex items-center space-x-4">
                    <div>
                        @if ($this->game->status['periodPrefix'] == 'Bottom')
                            <!-- Home team is batting -->
                            <img src="{{ $this->game->home->logo }}" class="h-10 w-10" />
                        @else
                            <!-- Away team is batting -->
                            <img src="{{ $this->game->away->logo }}" class="h-10 w-10" />
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="text-xs font-semibold">BATTER</div>
                        <div class="text-sm font-light tracking-wide">{{ $batter['shortName'] }}</div>
                        <div class="text-xs text-gray-400">
                            @foreach ($batterStats['splits']['categories'] as $stat)
                                @if ($stat['name'] == 'batting')
                                    {{ $stat['summary'] }}
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Last Play -->
                @isset($lastPlay['text'])
                    <div>
                        {{ $lastPlay['text']}}
                    </div>
                @endisset

            </div>

        @endif

    </div>

</div>
