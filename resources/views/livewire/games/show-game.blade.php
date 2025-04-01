<div wire:poll.30s="refresh">

    <div class="flex justify-center">

        <div class="flex grow items-center lg:mx-4 lg:py-2">

            <!-- Away Team -->
            <div class="flex items-center grow justify-end text-lg text-gray-900 font-bold">

                <div class="flex flex-col items-end min-w-12 pl-4">

                    <!-- Away rank & name -->
                    <div class="flex items-center">
                        @if ($game->away_rank > 0)
                            <span class="text-[11px] sm:text-sm lg:font-medium text-gray-500 dark:text-slate-300 font-normal mr-1.5">{{ $game->away_rank }}</span>
                        @endif
                        @if ($game->away_id > 0)
                            <a href="{{ route('team', $game->away_id) }}" class="hover:text-blue-800 dark:text-slate-300 dark:hover:text-white">
                                <span class="hidden md:flex font-normal lg:font-semibold lg:tracking-wide">{{ $game->away->location }}</span>
                                <span class="flex md:hidden text-[11px] sm:text-sm font-normal">{{ $game->away->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500 dark:text-slate-300">TBD</span>
                        @endif
                    </div>

                    <!-- Away record -->
                    @if (isset($game->away_records))
                        <div class="flex items-center font-light md:font-normal text-[10px] sm:text-[11px] md:text-xs space-x-1 text-gray-500 dark:text-slate-300 p-0">
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

                <!-- Away logo & score -->
                @if ($game->status_id != 1)
                    <a href="{{ route('team', $game->away_id) }}" class="px-3 md:px-6 shrink-0 cursor-pointer">

                        <span class="flex sm:hidden">
                            <x-game.team-logo :team="$game->away" size="8" />
                        </span>
                        <span class="hidden sm:flex md:hidden">
                            <x-game.team-logo :team="$game->away" size="9" />
                        </span>
                        <span class="hidden md:flex">
                            <x-game.team-logo :team="$game->away" size="10" />
                        </span>

                    </a>
                    <div class="text-3xl md:text-4xl lg:text-5xl text-center px-4 md:px-6 lg:px-8 font-semibold">
                        @if ($game->final && $game->away_runs < $game->home_runs)
                            <span class="text-gray-400">{{ $game->away_runs }}</span>
                        @else
                            <span class="text-gray-700 dark:text-slate-100">{{ $game->away_runs }}</span>
                        @endif
                    </div>
                @else
                    <a href="{{ route('team', $game->away_id) }}" class="pl-3 md:pl-6 shrink-0 cursor-pointer">
                        <span class="flex sm:hidden">
                            <x-game.team-logo :team="$game->away" size="8" />
                        </span>
                        <span class="hidden sm:flex md:hidden">
                            <x-game.team-logo :team="$game->away" size="9" />
                        </span>
                        <span class="hidden md:flex">
                            <x-game.team-logo :team="$game->away" size="10" />
                        </span>
                    </a>
                @endif
            </div>

            <!-- Status -->
            <div class="flex flex-col shrink items-center space-y-0.5 px-2 md:px-4 lg:px-6 text-sm font-light text-muted dark:text-lighter">

                    @if($game->status_id == 1)

                        <div @class([
                                'font-semibold md:text-base',
                                'text-red-600 dark:text-yellow-400' => $game->status_id == 2,
                            ])>
                            {{ $game->status['type']['shortDetail'] }}
                        </div>

                    @elseif ($game->status_id == 2)

                        <div class="flex items-center space-x-4">

                            <flux:icon.loading wire:loading wire:target="situation" class="size-4" />
                            
                            <x-game.inning :inning="explode(' ', $this->game->status['type']['shortDetail'])[1]" :type="explode(' ', $this->game->status['type']['shortDetail'])[0]"/>
                            <x-game.bases :runners="$this->runners" size="5" outs="1"/>

                        </div>

                    @endif

            </div>

            <!-- Home Team -->
            <div class="flex grow items-center justify-start text-lg text-gray-900 font-bold">

                <!-- Home logo & Score -->
                @if ($game->status_id != 1)
                    <div class="text-3xl md:text-4xl lg:text-5xl text-center px-4 md:px-6 lg:px-8 font-semibold">
                        @if ($game->final && $game->home_runs < $game->away_runs)
                            <span class="text-gray-400">{{ $game->home_runs }}</span>
                        @else
                            <span class="text-gray-700 dark:text-slate-100">{{ $game->home_runs }}</span>
                        @endif
                    </div>

                    <a href="{{ route('team', $game->home_id) }}" class="px-3 md:px-6 shrink-0 cursor-pointer">
                        <span class="flex sm:hidden">
                            <x-game.team-logo :team="$game->home" size="8" />
                        </span>
                        <span class="hidden sm:flex md:hidden">
                            <x-game.team-logo :team="$game->home" size="9" />
                        </span>
                        <span class="hidden md:flex">
                            <x-game.team-logo :team="$game->home" size="10" />
                        </span>
                    </a>
                @else
                    <a href="{{ route('team', $game->home_id) }}" class="pl-3 md:pl-6 shrink-0 cursor-pointer">
                        <span class="flex sm:hidden">
                            <x-game.team-logo :team="$game->home" size="8" />
                        </span>
                        <span class="hidden sm:flex md:hidden">
                            <x-game.team-logo :team="$game->home" size="9" />
                        </span>
                        <span class="hidden md:flex">
                            <x-game.team-logo :team="$game->home" size="10" />
                        </span>
                    </a>
                @endif

                <div class="flex flex-col items-start pr-4 min-w-12">

                    <!-- Home rank & name -->
                    <div class="flex items-center">
                        @if ($game->home_rank > 0)
                            <span class="text-[11px] sm:text-sm lg:font-medium text-gray-500 dark:text-slate-300 font-normal mr-1.5">{{ $game->home_rank }}</span>
                        @endif
                        @if ($game->home_id > 0)
                            <a href="{{ route('team', $game->home_id) }}" class="hover:text-blue-800 dark:text-slate-300 dark:hover:text-white">
                                <span class="hidden md:flex font-normal lg:font-semibold lg:tracking-wide">{{ $game->home->location }}</span>
                                <span class="flex md:hidden text-[11px] sm:text-sm font-normal">{{ $game->home->abbreviation }}</span>
                            </a>
                        @else
                            <span class="text-gray-500 dark:text-slate-300">TBD</span>
                        @endif
                    </div>

                    <!-- Home record -->
                    @if (isset($game->home_records))
                        <div class="flex items-center font-light md:font-normal text-[10px] sm:text-[11px] md:text-xs space-x-1 text-gray-500 dark:text-slate-300 p-0">
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
   