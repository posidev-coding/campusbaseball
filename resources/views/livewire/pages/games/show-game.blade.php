<div wire:poll.15s="refresh">

    <div class="-ml-8 -mr-8 -mt-8 pt-4">

        <div class="flex items-center space-x-4 py-2">

            <!-- Away Team -->
            <div class="flex items-center justify-end w-1/3 md:w-5/12 text-lg text-gray-900 font-bold">
                <div class="flex flex-col items-end">
                    <div class="flex items-center">
                        @if ($game->away_rank > 0)
                            <span class="text-sm text-gray-500 font-medium mr-1.5">{{ $game->away_rank }}</span>
                        @endif
                        @if ($game->away_id > 0)
                            <a href="{{ route('team', $game->away_id) }}" class="hover:text-blue-800">
                                <span class="hidden md:flex">{{ $game->away->location . ' ' . $game->away->name }}</span>
                                <span class="flex md:hidden">{{ $game->away->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500">TBD</span>
                        @endif
                    </div>

                    @if (isset($game->away_records))
                        <div class="flex items-center space-x-1 text-xs text-gray-500 p-0 font-light">
                            @foreach ($game->away_records as $rec)
                                @if ($rec['type'] == 'total')
                                    <div class="flex">{{ $rec['summary'] }}</div>
                                @endif
                                @if ($rec['type'] == 'vsconf')
                                    <div class="hidden md:flex">({{ $rec['summary'] }})</div>
                                @endif
                            @endforeach
                        </div>
                    @endif


                </div>
                @if ($game->status_id != 1)
                    <div class="px-3 md:px-6">
                        <img src="{{ $game->away->logos[0]['href'] }}" alt="{{ $game->away->abbreviation }}"
                            class="w-12 h-12" />
                    </div>
                    <div class="text-3xl md:text-4xl font-bold">
                        @if ($game->completed && $game->away_runs < $game->home_runs)
                            <span class="text-gray-400">{{ $game->away_runs }}</span>
                        @else
                            <span class="text-gray-700">{{ $game->away_runs }}</span>
                        @endif
                    </div>
                @else
                    <div class="pl-3 md:pl-6">
                        <img src="{{ $game->away->logos[0]['href'] }}" alt="{{ $game->away->abbreviation }}"
                            class="w-12 h-12" />
                    </div>
                @endif
            </div>

            <!-- Status -->
            <div class="w-1/3 md:w-2/12 flex flex-col items-center space-y-0.5 text-xs tracking-tighter text-gray-500">

                {{-- <flux:icon.loading wire:loading wire:target="refresh" class="size-4" /> --}}

                {{-- <div wire:loading.remove wire:target="refresh"> --}}
                    @if (!$game->completed && isset($game->broadcasts[0]['station']))
                        <span>{{ $game->broadcasts[0]['station'] }}</span>
                    @endif

                    <div class="font-semibold flex items-center">
                        <div @class([
                            'text-red-600' => $game->status_id == 2,
                        ])>
                            {{ $game->status['type']['shortDetail'] }}
                        </div>
                        @if ($game->status_id == 2 && isset($this->situation['outs']))
                            <flux:icon.dot />
                            <div class="text-black">
                                {{ $this->situation['outs'] . ($this->situation['outs'] == 1 ? ' out' : ' outs') }}
                            </div>
                        @endif
                    </div>

                    @if ($game->status_id == 2 && isset($this->situation['outs']))

                        <flux:icon.loading wire:loading wire:target="situation" class="size-4" />

                        <div wire:loading.remove wire:target="situation" class="BaseballBases">
                            <div class="BaseballBases__Wrapper flex relative justify-center">
                                <div @class([
                                    'diamond first-base border',
                                    'border-blue-600' => isset($this->situation['onFirst']),
                                ]) style="border-width: 4px; margin-bottom: falsepx">
                                </div>
                                <div @class([
                                    'diamond second-base border',
                                    'border-blue-600' => isset($this->situation['onFirst']),
                                ]) style="border-width: 4px; margin-bottom: 8px"></div>
                                <div @class([
                                    'diamond third-base border',
                                    'border-blue-600' => isset($this->situation['onFirst']),
                                ]) style="border-width: 4px; margin-bottom: falsepx">
                                </div>
                            </div>
                        </div>

                    @endif
                {{-- </div> --}}

            </div>

            <!-- Home Team -->
            <div class="flex items-center justify-start w-1/3 md:w-5/12 text-lg text-gray-900 font-bold">

                @if ($game->status_id != 1)
                    <div class="text-3xl md:text-4xl font-bold">
                        @if ($game->completed && $game->home_runs < $game->away_runs)
                            <span class="text-gray-400">{{ $game->home_runs }}</span>
                        @else
                            <span class="text-gray-700">{{ $game->home_runs }}</span>
                        @endif
                    </div>
                    <div class="px-3 md:px-6">
                        <img src="{{ $game->home->logos[0]['href'] }}" alt="{{ $game->home->abbreviation }}"
                            class="w-12 h-12" />
                    </div>
                @else
                    <div class="pr-3 md:pr-6">
                        <img src="{{ $game->home->logos[0]['href'] }}" alt="{{ $game->home->abbreviation }}"
                            class="w-12 h-12" />
                    </div>
                @endif

                <div class="flex flex-col items-start">

                    <div class="flex items-center">
                        @if ($game->home_rank > 0)
                            <span class="text-sm text-gray-500 font-medium mr-1.5">{{ $game->home_rank }}</span>
                        @endif
                        @if ($game->home_id > 0)
                            <a href="{{ route('team', $game->away_id) }}" class="hover:text-blue-800">
                                <span
                                    class="hidden md:flex">{{ $game->home->location . ' ' . $game->home->name }}</span>
                                <span class="flex md:hidden">{{ $game->home->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500">TBD</span>
                        @endif
                    </div>

                    @if (isset($game->home_records))
                        <div class="flex items-center space-x-1 text-xs text-gray-500 p-0 font-light">
                            @foreach ($game->home_records as $rec)
                                @if ($rec['type'] == 'total')
                                    <div class="flex">{{ $rec['summary'] }}</div>
                                @endif
                                @if ($rec['type'] == 'vsconf')
                                    <div class="hidden md:flex">({{ $rec['summary'] }})</div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    <div>
        {{-- Content --}}
        @if ($game->status_id > 1)
            <x-game.box-score :game="$game" :situation="$situation" />
        @endif
    </div>
    {{-- 
    <!-- Wrapping Grid -->
    <div class="mx-auto grid grid-cols-1 lg:grid-cols-12 gap-2 lg:gap-4">

        <div class="flex flex-col gap-2 order-3 lg:order-2 lg:col-span-6">

            @unless ($game->status_desc == 'Scheduled')
                <x-game-summary.playcast :drives="$summary['drives']" :scoring="$summary['scoring']" :home="$game->home" :away="$game->awayTeam" />
            @endunless

            <x-game-summary.game-articles :article="$summary['article']" :stories="$summary['news']" />

        </div>

        <div class="flex flex-col gap-2 order-1 lg:col-span-3">

            <x-game-summary.venue :venue="$summary['venue']" />
            @if ($game->status_desc == 'Scheduled')
                @if (isset($summary['prediction']) && !empty($summary['prediction']))
                    <x-game-summary.prediction wire:ignore :game="$summary['prediction']" />
                @endif
                <x-game-summary.playmakers :game="$summary['leaders']" />
            @else
                <x-game-summary.playmakers :game="$summary['leaders']" />
                @if (isset($summary['prediction']) && !empty($summary['prediction']))
                    <x-game-summary.prediction wire:ignore :game="$summary['prediction']" />
                @endif
            @endif

            <x-game-summary.probability :game="$summary['probability']" />

        </div>

        <div class="flex flex-col gap-2 order-2 lg:order-3 lg:col-span-3">

            <x-game-summary.game-contests :game="$game->id" />

            @foreach ($summary['standings']['groups'] as $conference)
                <x-game-summary.conference-standings :conference="$conference" :teams="$game->teams" />
            @endforeach

            <x-game-summary.team-stats :game="$summary['boxscore']" />

        </div>

    </div> --}}

</div>
