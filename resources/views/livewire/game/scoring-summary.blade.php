<div wire:init="fetchPlays" class="flex justify-center">

    <div class="border dark:border-slate-500 rounded-lg mt-2 md:mt-4 lg:mx-4 max-w-4xl grow">
        
        @if($plays && count($plays) > 0)
            <flux:table>
                <flux:table.columns class="bg-slate-100 dark:bg-slate-700">
                    <flux:table.column>
                        <div class="pl-2.5 lg:pl-4"></div>
                    </flux:table.column>
                    <flux:table.column>Scoring Summary</flux:table.column>
                    <flux:table.column align="end">{{ $game->away->abbreviation }}</flux:table.column>
                    <flux:table.column align="end">
                        <div class="pr-2.5 lg:pr-4">{{ $game->home->abbreviation }}</div>
                    </flux:table.column>
                </flux:table.columns>
            
                <flux:table.rows>
                    @foreach ($plays as $play)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center pl-2.5 lg:pl-4 space-x-2.5">
                                    <x-game.team-logo :team="$play->team" size="6" />
                                    @if($play['inning_type'] == 'Top')
                                        <flux:icon.chevron-up variant="micro"/>
                                    @else
                                        <flux:icon.chevron-down variant="micro"/>
                                    @endif
                                    <div class="-ml-1 text-gray-800 dark:text-zinc-300">{{ str_replace(' Inning', '', $play['inning_display']) }}</div>
                                    <div>
                                        <x-game.bases :runners="$play['runners']" size="4"/>
                                    </div>
                                    <div class="text-black dark:text-zinc-200 text-xs font-medium">
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
