<div>

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('teams') }}">Teams</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $team->location . ' ' . $team->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    
    {{-- <flux:heading size="xl" level="1">{{ $team->location }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">{{ $team->name }}</flux:text> --}}

    <div class="mt-4 mb-2 lg:mt-8 lg:mb-4 flex grow items-center justify-between text-lg text-gray-900 font-bold space-x-4">

        <div class="flex items-center space-x-4">
            
            <!-- Logo -->
            <span class="flex sm:hidden">
                <x-game.team-logo :team="$team" size="12" />
            </span>
            <span class="hidden sm:flex md:hidden">
                <x-game.team-logo :team="$team" size="14" />
            </span>
            <span class="hidden md:flex lg:hidden">
                <x-game.team-logo :team="$team" size="16" />
            </span>
            <span class="hidden lg:flex">
                <x-game.team-logo :team="$team" size="20" />
            </span>

            <!-- Rank & name -->
            <div class="flex flex-col items-center">

                <div class="flex text-base md:text-xl lg:text-2xl">
                    @if($rank > 0)
                        <span class="lg:font-medium text-gray-500 dark:text-slate-300 font-normal mr-1.5">{{ $rank }}</span>
                    @endif

                    <div class="flex flex-col">
                        <p class="flex items-center space-x-1.5 dark:text-slate-300">
                            <span class="flex font-normal lg:font-semibold lg:tracking-wide">{{ $team->location }}</span>
                            <span class="flex font-extralight lg:font-light lg:tracking-wide">{{ $team->name }}</span>
                        </p>
                        
                        <!-- Follow & Record -->
                        @if(isset($team->record))
                            <div class="flex items-center font-light md:font-normal text-[13px] md:text-[15px] space-x-2 text-gray-500 dark:text-slate-300 p-0">
                                
                                <div class="flex">{{ $team->record->summary }}</div>
                                
                                <!-- Follow -->
                                <div class="flex md:hidden">
                                    @if($following)
                                        <flux:button variant="primary" size="xs" wire:click="toggle()" class="cursor-pointer rounded-xl bg-transparent border-blue-600 text-blue-600">Following</flux:button>
                                    @else
                                        <flux:button variant="primary" size="xs" wire:click="toggle()" class="cursor-pointer rounded-xl bg-blue-600">Follow</flux:button>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        <!-- Follow -->
        <div class="hidden md:flex">
            @if($following)
                <flux:button variant="primary" size="sm" wire:click="toggle()" class="cursor-pointer rounded-xl bg-transparent border-blue-600 text-blue-600">Following</flux:button>
            @else
                <flux:button variant="primary" size="sm" wire:click="toggle()" class="cursor-pointer rounded-xl bg-blue-600">Follow</flux:button>
            @endif
        </div>

    </div>

    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="home">Home</flux:tab>
            <flux:tab name="schedule">Schedule</flux:tab>
            <flux:tab name="roster">Roster</flux:tab>
            <flux:tab name="stats">Stats</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="home">
            <div class="flex items-start space-x-4">

                <div class="flex flex-col w-full md:w-1/2 lg:w-1/3 bg-card border dark:border-muted rounded-lg lg:mx-4 max-w-4xl hover:border-gray-400">
                    
                    <flux:table>
    
                        <flux:table.columns class="bg-card-header">
                            <flux:table.column><p class="pl-4 md:pl-6">{{ config('espn.year') . ' Results' }}</p></flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>
    
                        <flux:table.rows class="bg-card">
    
                            @foreach ($this->games as $game)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex items-center pl-4 md:pl-6 space-x-1">
                                            <div class="flex text-muted font-extralight text-xs w-8">
                                                {{ $game->game_date->format('n/j') }}
                                            </div>
                                            <div class="flex">
                                                {{ $game->away_id == $team->id ? '@' : 'vs' }}
                                            </div>
                                            <div class="flex">
                                                <x-game.team-logo :team="$game->away_id == $team->id ? $game->home : $game->away" size="5" />
                                            </div>
                                            @if($game->away_id == $team->id && isset($game->home_rank) && $game->home_rank > 0)
                                                <div class="flex text-muted font-light text-xs">
                                                    {{ $game->home_rank }}
                                                </div>
                                            @endif
                                            @if($game->home_id == $team->id && isset($game->away_rank) && $game->away_rank > 0)
                                                <div class="flex text-muted font-light text-xs">
                                                    {{ $game->away_rank }}
                                                </div>
                                            @endif
                                            <div class="flex">
                                                {{ $game->away_id == $team->id ? $game->home->location : $game->away->location }}
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell align="end">
                                        <div class="flex items-center justify-end pr-4 md:pr-6">
                                            <div class="flex justify-center w-8 {{ ($game->away_id == $team->id && $game->away_winner || $game->home_id == $team->id && $game->home_winner) ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ($game->away_id == $team->id && $game->away_winner || $game->home_id == $team->id && $game->home_winner) ? 'W' : 'L' }}
                                            </div>
                                            <div class="flex justify-center w-12">
                                                {{ $game->away_winner ? ($game->away_runs . ' - ' . $game->home_runs) : ($game->home_runs . ' - ' . $game->away_runs) }}
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach

                        </flux:table.rows>

                    </flux:table>

                </div>

                <div class="flex flex-col w-full md:w-1/2 lg:w-2/3 space-y-2">
                    <flux:heading>Upcoming Games</flux:heading>
                    @foreach ($this->upcoming as $game)
                        <x-games.card :game="$game" />
                    @endforeach
                </div>
        
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="schedule">
            <div class="flex flex-col space-y-2">
                <flux:heading>Full Schedule</flux:heading>
                @foreach ($this->schedule as $game)
                    <x-games.card :game="$game" />
                @endforeach
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="roster">Roster...</flux:tab.panel>
        <flux:tab.panel name="stats">Stats...</flux:tab.panel>
    </flux:tab.group>

</div>
