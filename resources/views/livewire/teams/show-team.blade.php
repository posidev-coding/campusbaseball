<div>

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('teams') }}">Teams</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $team->location . ' ' . $team->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    
    {{-- <flux:heading size="xl" level="1">{{ $team->location }}</flux:heading>
    <flux:text class="mb-6 mt-2 text-base">{{ $team->name }}</flux:text> --}}

    <div class="mt-4 mb-2 lg:mt-8 lg:mb-4 flex grow items-center justify-between text-lg text-gray-900 font-bold space-x-4">

        <!-- Logo -->
        <div class="flex space-x-4">

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
            <div class="flex flex-col items-start">

                <div class="flex text-base md:text-xl lg:text-2xl">
                    @if($rank > 0)
                        <span class="lg:font-medium text-gray-500 dark:text-slate-300 font-normal mr-1.5">{{ $rank }}</span>
                    @endif

                    <div class="flex flex-col">
                        <p class="dark:text-slate-300">
                            <span class="flex font-normal lg:font-semibold lg:tracking-wide">{{ $team->location . ' ' . $team->name }}</span>
                            {{-- <span class="flex md:hidden font-normal">{{ $team->abbreviation }}</span> --}}
                        </p>
                        
                        <!-- Record -->
                        @if(isset($team->record))
                            <div class="flex items-center font-light md:font-normal text-[12px] sm:text-[13px] md:text-sm space-x-1 text-gray-500 dark:text-slate-300 p-0">
                                <div class="flex">{{ $team->record->summary }}</div>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        <!-- Follow -->
        @if($following)
            <flux:button variant="filled" icon="x-mark" wire:click="toggle()" class="cursor-pointer">Unfollow</flux:button>
        @else
            <flux:button variant="primary" icon="plus-circle" wire:click="toggle()" class="cursor-pointer">Follow</flux:button>
        @endif
    </div>

    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="schedule">Schedule</flux:tab>
            <flux:tab name="roster">Roster</flux:tab>
            <flux:tab name="stats">Stats</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="schedule">Schedule...</flux:tab.panel>
        <flux:tab.panel name="roster">Roster...</flux:tab.panel>
        <flux:tab.panel name="stats">Stats...</flux:tab.panel>
    </flux:tab.group>

</div>
