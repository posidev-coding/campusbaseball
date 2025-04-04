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

    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">

        @foreach ($this->teams as $team)
            <a href="{{ route('team', $team->id) }}"
                class="flex flex-col relative items-center bg-card border border-gray-150 dark:border-muted rounded-lg p-2 cursor-pointer hover:border-gray-400">

                @isset($team->logos[0])

                    <div clas="flex">
                        <img src="{{ $team->logo }}" alt="{{ $team->abbreviation }}" class="w-24 h-24 flex dark:hidden" />
                        <img src="{{ $team->darkLogo }}" alt="{{ $team->abbreviation }}" class="w-24 h-24 hidden dark:flex" />
                      </div>

                @endisset

                <flux:separator text="{{ $team->location }}" />
                <div class="text-muted tracking-wider font-thin text-base">{{ $team->name }}</div>

                <div class="absolute top-1.5 right-2.5 text-muted text-[10px]">{{ $team->record->summary ?? null }}</div>

                @isset($team->live)

                    <div class="absolute flex items-center top-1.5 left-2.5">
                        <div class="flex">
                            <span class="relative flex size-1.5">
                                <span
                                    class="absolute flex inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex size-1.5 rounded-full bg-red-700 opacity-60"></span>
                            </span>
                        </div>

                        <div class="text-[10px] ml-1.5">
                            {{ $team->live->away_id == $team->id ? '@ ' . $team->live->home->abbreviation : 'vs ' . $team->live->away->abbreviation }}
                        </div>

                    </div>
                @endisset

            </a>
        @endforeach

    </div>

</div>
