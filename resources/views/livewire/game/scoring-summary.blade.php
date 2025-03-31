<div wire:init="fetchPlays" class="flex justify-center">

    <div @class([
        'border rounded-lg mt-2 md:mt-4 lg:mx-4 max-w-4xl grow px-4',
    ])>
        {{-- <flux:header>Scoring Summary</flux:header>
        <flux:separator /> --}}
        
        @if($plays && count($plays) > 0)
            <flux:table class="px-4">
                <flux:table.columns>
                    <flux:table.column></flux:table.column>
                    <flux:table.column>Scoring Summary</flux:table.column>
                    <flux:table.column align="end">{{ $game->away->abbreviation }}</flux:table.column>
                    <flux:table.column align="end">{{ $game->home->abbreviation }}</flux:table.column>
                </flux:table.columns>
            
                <flux:table.rows>
                    @foreach ($plays as $play)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center space-x-2.5">
                                    <img src="{{ $play->team->logos[0]['href'] }}" class="h-6 w-6" />
                                    @if($play['inning_type'] == 'Top')
                                        <flux:icon.chevron-up variant="micro"/>
                                    @else
                                        <flux:icon.chevron-down variant="micro"/>
                                    @endif
                                    <div class="-ml-1 text-gray-800">{{ str_replace(' Inning', '', $play['inning_display']) }}</div>
                                    <div>
                                        <x-game.bases :runners="$play['runners']" small/>
                                    </div>
                                    <div class="text-black text-xs font-medium">
                                        {{ $play['outs'] . ($play['outs'] == 1 ? ' out' : ' outs') }}
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-wrap">{{ $play['text'] }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $play['away_runs'] }}</flux:table.cell>
                            <flux:table.cell align="end">{{ $play['home_runs'] }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <x-skeleton.list/>
        @endif

    </div>

</div>
