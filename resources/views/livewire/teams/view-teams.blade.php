<div>

    <div class="flex items-center justify-center no-wrap mb-8 overflow-x-auto">

        <flux:button.group>
            @foreach ($this->conferences as $conf)
                @if ($conf->id == $this->conference)
                    <flux:button variant="primary" wire:click="clearConf()" class="cursor-pointer uppercase">
                        {{ $conf->short_name }}
                    </flux:button>
                @else
                    <flux:button wire:click="setConf('{{ $conf->id }}')" class="cursor-pointer uppercase">
                        {{ $conf->short_name }}
                    </flux:button>
                @endif
            @endforeach
        </flux:button.group>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        @foreach ($this->teams as $team)
            <a href="{{ route('team', $team->id) }}" @class([
                'flex items-center relative space-x-4 bg-white shadow hover:shadow-lg rounded-lg p-2 cursor-pointer hover:border-gray-400',
            ])>
                @isset($team->logos[0])
                    <img src="{{ $team->logo }}" class="h-10 w-10" />
                @endisset
                <div class="flex flex-col">

                    <div class="text-black text-lg font-medium tracking-wide">
                        {{ $team->location . ' ' . $team->name }}
                    </div>

                    @if ($team->record)
                        <div class="text-gray-400 text-sm font-light">({{ $team->record->summary }})</div>
                    @endif
                </div>

                @isset($team->live)
                    <div class="absolute flex flex-row-reverse items-center bottom-1.5 right-2.5">
                        <div class="flex">
                            <span class="relative flex size-1.5">
                                <span
                                    class="absolute flex inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex size-1.5 rounded-full bg-red-700 opacity-60"></span>
                            </span>
                        </div>

                        <div class="text-[8px] mr-2">
                            {{ $team->live->away_id == $team->id ? '@ ' . $team->live->home->abbreviation : 'vs ' . $team->live->away->abbreviation }}
                        </div>

                    </div>
                @endisset

            </a>
        @endforeach

    </div>

</div>
