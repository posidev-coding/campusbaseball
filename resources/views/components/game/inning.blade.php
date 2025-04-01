@props([
  'inning', 
  'type'
])

<div class="flex flex-col justify-items-center -space-y-1 min-w-7">
    @if($type == 'Top')

        <flux:icon.caret-up-fill variant="micro" class="self-center text-black dark:text-blue-400"/>
        {{-- <flux:icon.caret-up-fill variant="tiny" class="self-center flex md:hidden text-black dark:text-blue-400"/> --}}

        <div class="self-center flex text-sm md:text-base text-gray-800 dark:text-light">{{ str_replace(' Inning', '', $inning) }}</div>

        <flux:icon.caret-down-fill variant="micro" class="self-center text-light/50 dark:text-slate-700"/>
        {{-- <flux:icon.caret-down-fill variant="tiny" class="self-center flex md:hidden text-light/50 dark:text-slate-700"/> --}}

    @else

        <flux:icon.caret-up-fill variant="micro" class="self-center text-light/50 dark:text-slate-700"/>
        {{-- <flux:icon.caret-up-fill variant="tiny" class="self-center flex md:hidden text-light/50 dark:text-slate-700"/> --}}

        <div class="self-center flex text-sm md:text-base text-gray-800 dark:text-light">{{ str_replace(' Inning', '', $inning) }}</div>

        <flux:icon.caret-down-fill variant="micro" class="self-center text-black dark:text-blue-400"/>
        {{-- <flux:icon.caret-down-fill variant="tiny" class="self-center flex md:hidden text-black dark:text-blue-400"/> --}}

    @endif
</div>