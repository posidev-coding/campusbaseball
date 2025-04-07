<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" href="{{ $icon ?? asset('images/campusbaseball.svg') }}">
        <title>{{ $title ?? null ? $title . ' | ' . config('app.name') : config('app.name') }}</title>

        <link rel="stylesheet" href="https://use.typekit.net/zrd1haj.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @fluxAppearance
    </head>

    <body class="font-proxima min-h-screen bg-body">

        <flux:header container class="shadow-xl uppercase bg-[#0b1d40] dark:bg-darkest">
            
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('home') }}" class="flex aspect-square size-8 mr-5 items-center justify-center rounded-md">
                <x-app-logo-icon />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item :href="route('scores')" :current="request()->routeIs('scores')" wire:navigate>{{ __('Scores') }}</flux:navbar.item>
                <flux:navbar.item :href="route('standings')" :current="request()->routeIs('standings')" wire:navigate>{{ __('Standings') }}</flux:navbar.item>
                <flux:navbar.item :href="route('teams')" :current="request()->routeIs('teams')" wire:navigate>{{ __('Teams') }}</flux:navbar.item>
                <flux:navbar.item :href="route('rankings')" :current="request()->routeIs('rankings')" wire:navigate>{{ __('Rankings') }}</flux:navbar.item>
                <flux:navbar.item :href="route('stats')" :current="request()->routeIs('stats')" wire:navigate>{{ __('Stats') }}</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">

                    <flux:button icon:trailing="user-circle" variant="primary" class="text-white bg-transparent shadow-none hover:bg-white/10">Account</flux:button>

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-left text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:radio.group x-data size="xs" variant="segmented" x-model="$flux.appearance">
                            <flux:radio value="light" icon="sun"></flux:radio>
                            <flux:radio value="dark" icon="moon"></flux:radio>
                            <flux:radio value="system" icon="computer-desktop"></flux:radio>
                        </flux:radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        {{-- <flux:menu.separator /> --}}

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>

                    </flux:menu>
                </flux:dropdown>
            @else                    
                <div class="flex space-x-2">
                    <flux:button href="{{ route('login') }}" variant="primary">Log in</flux:button>
                    <flux:button href="{{ route('register') }}">Register</flux:button>
                </div>
            @endauth
        </flux:header>
        
        <flux:sidebar stashable sticky
            class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:navlist variant="outline">
                <flux:navlist.item :href="route('scores')" :current="request()->routeIs('scores')" wire:navigate>{{ __('Scores') }}</flux:navlist.item>
                <flux:navlist.item :href="route('standings')" :current="request()->routeIs('standings')" wire:navigate>{{ __('Standings') }}</flux:navlist.item>
                <flux:navlist.item :href="route('teams')" :current="request()->routeIs('teams')" wire:navigate>{{ __('Teams') }}</flux:navlist.item>
                <flux:navlist.item :href="route('rankings')" :current="request()->routeIs('rankings')" wire:navigate>{{ __('Rankings') }}</flux:navlist.item>
                <flux:navlist.item :href="route('stats')" :current="request()->routeIs('stats')" wire:navigate>{{ __('Stats') }}</flux:navlist.item>
            </flux:navlist>
            @auth    
                <flux:spacer />

                <flux:navlist variant="outline">
                    <flux:navlist.item icon="arrows-up-down" href="{{ route('feeds') }}">Feeds</flux:navlist.item>
                    <flux:navlist.item icon="cog-6-tooth" href="{{ route('settings.profile') }}">Settings</flux:navlist.item>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:navlist.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log Out') }}
                        </flux:navlist.item>
                    </form>
                    {{-- <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item> --}}
                </flux:navlist>
            @endauth
        </flux:sidebar>
        
        <flux:main container>
            {{ $slot }}
        </flux:main>
        
        @fluxScripts
        <flux:toast position="top right" />

    </body>

</html>
