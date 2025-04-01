<div wire:init="fetchPlays" class="flex justify-center">

    <div class="border dark:border-muted rounded-lg mt-2 md:mt-4 lg:mx-4 max-w-4xl grow">
        
        @if($plays && count($plays) > 0)
            <flux:table>
                <flux:table.columns class="bg-card-header">
                    <flux:table.column>
                        <div class="pl-2.5 lg:pl-4"></div>
                    </flux:table.column>
                    <flux:table.column>Scoring Summary</flux:table.column>
                    <flux:table.column align="end">{{ $game->away->abbreviation }}</flux:table.column>
                    <flux:table.column align="end">
                        <div class="pr-2.5 lg:pr-4">{{ $game->home->abbreviation }}</div>
                    </flux:table.column>
                </flux:table.columns>
            
                <flux:table.rows class="bg-card">
                    @foreach ($plays as $play)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center pl-2.5 lg:pl-4 space-x-4">
                                    
                                    <div class="flex w-8">
                                        <x-game.team-logo :team="$play->team" size="6" />
                                    </div>
                                    
                                    <x-game.inning :inning="$play['inning_display']" :type="$play['inning_type']" />

                                    <div class="flex w-8">
                                        <x-game.bases :runners="$play['runners']" size="4"/>
                                    </div>

                                    <!-- MAKE SOME OUTS -->
                                    <div class="text-black dark:text-slate-200 text-xs font-medium">
                                        {{ $play['outs'] . ($play['outs'] == 1 ? ' out' : ' outs') }}
                                    </div>

                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-wrap">{{ $play['text'] }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $play['away_runs'] }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="pr-2.5 lg:pr-4">{{ $play['home_runs'] }}</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <x-skeleton.list/>
        @endif

    </div>

</div>
