<div wire:poll.30s="refresh">

    <div class="-ml-8 -mr-8 -mt-8 pt-4">

        <div class="flex items-center space-x-4 py-2">

            <!-- Away Team -->
            <div class="flex items-center justify-end w-1/3 md:w-5/12 text-lg text-gray-900 font-bold">
                <div class="flex flex-col items-end">
                    <div class="flex items-center">
                        @if ($game->away_rank > 0)
                            <span class="text-sm text-gray-500 dark:text-slate-300 font-medium mr-1.5">{{ $game->away_rank }}</span>
                        @endif
                        @if ($game->away_id > 0)
                            <a href="{{ route('team', $game->away_id) }}" class="hover:text-blue-800 dark:text-slate-300 dark:hover:text-white">
                                <span class="hidden md:flex">{{ $game->away->location . ' ' . $game->away->name }}</span>
                                <span class="flex md:hidden">{{ $game->away->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500 dark:text-slate-300">TBD</span>
                        @endif
                    </div>

                    @if (isset($game->away_records))
                        <div class="flex items-center space-x-1 text-xs text-gray-500 dark:text-slate-300 p-0 font-light">
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
                        <x-game.team-logo :team="$game->away" size="10" />
                    </div>
                    <div class="text-3xl md:text-4xl font-bold">
                        @if ($game->final && $game->away_runs < $game->home_runs)
                            <span class="text-gray-400">{{ $game->away_runs }}</span>
                        @else
                            <span class="text-gray-700 dark:text-slate-100">{{ $game->away_runs }}</span>
                        @endif
                    </div>
                @else
                    <div class="pl-3 md:pl-6">
                        <x-game.team-logo :team="$game->away" size="10" />
                    </div>
                @endif
            </div>

            <!-- Status -->
            <div class="w-1/3 md:w-2/12 flex flex-col items-center space-y-0.5 text-sm font-light text-gray-500 dark:text-slate-300">

                    @if (!$game->final && isset($game->broadcasts[0]['station']))
                        <span>{{ $game->broadcasts[0]['station'] }}</span>
                    @endif

                    <div class="font-semibold flex items-center">
                        <div @class([
                            'text-red-600 dark:text-yellow-400' => $game->status_id == 2,
                        ])>
                            {{ $game->status['type']['shortDetail'] }}
                        </div>
                        @if ($game->status_id == 2 && isset($this->situation['outs']))
                            <flux:icon.dot />
                            <div class="text-black dark:text-slate-300">
                                {{ $this->situation['outs'] . ($this->situation['outs'] == 1 ? ' out' : ' outs') }}
                            </div>
                        @endif
                    </div>

                    @if ($game->status_id == 2 && isset($this->situation['outs']))

                        <flux:icon.loading wire:loading wire:target="situation" class="size-4" />
                        
                        <x-game.bases :runners="$this->runners" size="6"/>

                    @endif

            </div>

            <!-- Home Team -->
            <div class="flex items-center justify-start w-1/3 md:w-5/12 text-lg text-gray-900 font-bold">

                @if ($game->status_id != 1)
                    <div class="text-3xl md:text-4xl font-bold">
                        @if ($game->final && $game->home_runs < $game->away_runs)
                            <span class="text-gray-400">{{ $game->home_runs }}</span>
                        @else
                            <span class="text-gray-700 dark:text-slate-100">{{ $game->home_runs }}</span>
                        @endif
                    </div>
                    <div class="px-3 md:px-6">
                        <x-game.team-logo :team="$game->home" size="10" />
                    </div>
                @else
                    <div class="pr-3 md:pr-6">
                        <x-game.team-logo :team="$game->home" size="10" />
                    </div>
                @endif

                <div class="flex flex-col items-start">

                    <div class="flex items-center">
                        @if ($game->home_rank > 0)
                            <span class="text-sm text-gray-500 dark:text-slate-300 font-medium mr-1.5">{{ $game->home_rank }}</span>
                        @endif
                        @if ($game->home_id > 0)
                            <a href="{{ route('team', $game->away_id) }}" class="hover:text-blue-800 dark:text-slate-300 dark:hover:text-white">
                                <span
                                    class="hidden md:flex">{{ $game->home->location . ' ' . $game->home->name }}</span>
                                <span class="flex md:hidden">{{ $game->home->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500 dark:text-slate-300">TBD</span>
                        @endif
                    </div>

                    @if (isset($game->home_records))
                        <div class="flex items-center space-x-1 text-xs text-gray-500 dark:text-slate-300 p-0 font-light">
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
        @if ($game->status_id > 1)
            <x-game.box-score :game="$game" :situation="$situation" />
            @if(isset($game->resources['plays']))
                <livewire:game.scoring-summary :game="$game"/>
            @endif
        @endif
    </div>
</div>
   