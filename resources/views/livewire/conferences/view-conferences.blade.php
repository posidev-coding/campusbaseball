<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        @foreach ($this->conferences as $conf)

            <a href="{{ route('conference', $conf->id) }}" 
                class="flex flex-col bg-card border dark:border-muted rounded-lg lg:mx-4 max-w-4xl grow cursor-pointer hover:border-gray-400">
                
                <flux:table>

                    <flux:table.columns class="bg-card-header">
                        <flux:table.column><p class="pl-6 md:pl-8">{{ $conf->name }}</p></flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows class="bg-card">

                        @foreach ($conf->standings as $standing)

                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center pl-6 md:pl-8 space-x-2 md:space-x-4">
                                        <div class="flex w-10">
                                            <x-game.team-logo :team="$standing->team" size="8" />
                                        </div>
                                        <div class="flex">
                                            {{ $standing->team->location }}
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="end"><p class="pr-6 md:pr-8">{{ $standing->record }}</p></flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </a>

        @endforeach

    </div>

</div>
