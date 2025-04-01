@props([
  'inning', 
  'type'
])

<div class="flex flex-col -space-y-0.5">
    @if($type == 'Top')
        <flux:icon.caret-up-fill variant="micro" class="text-black dark:text-cyan-400"/>
        <div class="-ml-1 text-gray-800 dark:text-light">{{ str_replace(' Inning', '', $inning) }}</div>
        <flux:icon.caret-down-fill variant="micro" class="text-light/50 dark:text-slate-700"/>
    @else
        <flux:icon.caret-up-fill variant="micro" class="dark:text-slate-700"/>
        <div class="-ml-1 text-gray-800 dark:text-light">{{ str_replace(' Inning', '', $inning) }}</div>
        <flux:icon.caret-down-fill variant="micro" class="text-light text-black dark:text-cyan-400"/>
    @endif
</div>