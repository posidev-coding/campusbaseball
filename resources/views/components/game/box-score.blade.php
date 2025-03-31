<div class="flex justify-center">

    <div @class([
        'border rounded-lg mt-2 md:mt-4 lg:mx-4 grow',
        'max-w-5xl' => count($this->game->away_box) > 12,
        'max-w-4xl' => count($this->game->away_box) > 9,
        'max-w-3xl' => count($this->game->away_box) > 0,
        'max-w-sm' => count($this->game->away_box) == 0
    ])>
        <div class="flex items-stretch w-full">
            <div class="flex flex-col">
                <div class="bg-slate-100 h-7 border-b"></div>
                <div class="flex items-center space-x-2 px-2 h-7 border-b">
                    @isset($this->game->away->logos[0])
                        <img src="{{ $this->game->away->logos[0]['href'] }}" class="h-6 w-6" />
                    @endisset
                    <div @class([
                        'text-sm',
                        'font-light',
                        'text-black',
                        'text-gray-400' => $this->game->status_id == 3 && !$this->game->away_winner,
                    ])>{{ $this->game->away->abbreviation ?? 'N/A' }}</div>
                </div>
                <div class="flex items-center space-x-2 px-2 h-7">
                    @isset($this->game->home->logos[0])
                        <img src="{{ $this->game->home->logos[0]['href'] }}" class="h-6 w-6" />
                    @endisset
                    <div @class([
                        'text-sm',
                        'font-light',
                        'text-black',
                        'text-gray-400' => $this->game->status_id == 3 && !$this->game->home_winner,
                    ])>{{ $this->game->home->abbreviation ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="flex grow">

                @foreach($innings as $inning)

                    <div class="flex flex-col grow border-l text-sm font-light">
                        <div @class([
                            'h-7 place-content-center text-center text-gray-500 bg-slate-100 border-b',
                            'text-gray-900 font-semibold' =>!$this->game->final && $inning['away']['inning'] == $this->game->status['period'],
                        ])>{{ $inning['away']['inning'] }}</div>
                        <div @class([
                            'h-7 place-content-center text-center text-gray-700 border-b',
                            'bg-blue-50' =>
                                !$this->game->final && $inning['away']['inning'] == $this->game->status['period'] && $this->game->status['periodPrefix'] == 'Top',
                        ])>{{ $inning['away']['runs'] }}</div>
                        <div @class([
                            'h-7 place-content-center text-center text-gray-700',
                            'bg-blue-50' =>
                                !$this->game->final && $box['inning'] == $this->game->status['period'] && $this->game->status['periodPrefix'] == 'Bottom',
                        ])>{{ $inning['home']['runs'] }}</div>
                    </div>

                @endforeach

                <div class="flex flex-col grow border-l font-semibold text-sm">
                    <div class="h-7 place-content-center text-center text-gray-500 bg-slate-100 border-b">R</div>
                    <div class="h-7 place-content-center text-center text-gray-700 border-b">
                        {{ $this->game->away_runs ?? '-' }}
                    </div>
                    <div class="h-7 place-content-center text-center text-gray-700">
                        {{ $this->game->home_runs ?? '-' }}
                    </div>
                </div>
                <div class="flex flex-col grow border-l font-semibold text-sm">
                    <div class="h-7 place-content-center text-center text-gray-500 bg-slate-100 border-b">H</div>
                    <div class="h-7 place-content-center text-center text-gray-700 border-b">
                        {{ $this->game->away_hits ?? '-' }}
                    </div>
                    <div class="h-7 place-content-center text-center text-gray-700">
                        {{ $this->game->home_hits ?? '-' }}
                    </div>
                </div>
                <div class="flex flex-col grow border-l font-semibold text-sm">
                    <div class="h-7 place-content-center text-center text-gray-500 bg-slate-100 border-b">E</div>
                    <div class="h-7 place-content-center text-center text-gray-700 border-b">
                        {{ $this->game->away_errors ?? '-' }}
                    </div>
                    <div class="h-7 place-content-center text-center text-gray-700">
                        {{ $this->game->home_errors ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        @if (isset($this->situation['pitcher']))

            @php
                $pitcher = isset($this->situation['pitcher']['athlete']['$ref']) ? Http::get($this->situation['pitcher']['athlete']['$ref'])->json() : null;
                $pitcherStats = isset($this->situation['pitcher']['statistics']['$ref']) ? Http::get($this->situation['pitcher']['statistics']['$ref'])->json() : null;
                $batter = isset($this->situation['batter']['athlete']['$ref']) ? Http::get($this->situation['batter']['athlete']['$ref'])->json() : null;
                $batterStats = isset($this->situation['batter']['statistics']['$ref']) ? Http::get($this->situation['batter']['statistics']['$ref'])->json() : null;
                $lastPlay = isset($this->situation['lastPlay']['$ref']) ? Http::get($this->situation['lastPlay']['$ref'])->json() : null;
            @endphp

            <div class="border-t p-2 flex items-center justify-center space-x-12">

                <!-- Pitcher -->
                <div class="flex items-center space-x-4">
                    <div>
                        @if ($this->game->status['periodPrefix'] == 'Top')
                            <!-- Home team is pitching -->
                            <img src="{{ $this->game->home->logos[0]['href'] }}" class="h-10 w-10" />
                        @else
                            <!-- Away team is pitching -->
                            <img src="{{ $this->game->away->logos[0]['href'] }}" class="h-10 w-10" />
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
                            <img src="{{ $this->game->home->logos[0]['href'] }}" class="h-10 w-10" />
                        @else
                            <!-- Away team is batting -->
                            <img src="{{ $this->game->away->logos[0]['href'] }}" class="h-10 w-10" />
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

                <!-- Bases -->
                @if ($game->status_id == 2 && isset($this->situation['outs']))
                <div class="flex flex-col space-y-1 text-center">
                    <div class="BaseballBases">
                        <div class="BaseballBases__Wrapper flex relative justify-center">
                            <div @class([
                                'diamond first-base border',
                                'border-blue-600' => isset($this->situation['onFirst']),
                            ]) style="border-width: 7px;"></div>
                            <div @class([
                                'diamond second-base border',
                                'border-blue-600' => isset($this->situation['onFirst']),
                            ])
                                style="border-width: 7px; margin-bottom: 14px"></div>
                            <div @class([
                                'diamond third-base border',
                                'border-blue-600' => isset($this->situation['onFirst']),
                            ]) style="border-width: 7px;"></div>
                        </div>
                    </div>
                    <div class="text-gray-700 text-[10px]">
                        {{ $this->situation['outs'] . ($this->situation['outs'] == 1 ? ' out' : ' outs') }}
                    </div>
                </div>
                @endif

                <!-- Last Play -->
                {{-- @if($lastPlay)
                    <div>
                        {{ $lastPlay['text'] }}
                    </div>
                @endif --}}

            </div>

        @endif

    </div>
    
</div>
