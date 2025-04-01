@props([
  'inning', 
  'type'
])

<div class="flex flex-col -space-y-1.5">
    @if($type == 'Top')
        <flux:icon.caret-up-fill variant="micro" class="text-black dark:text-cyan-400"/>
        <div class="-ml-1 text-gray-800 dark:text-muted">{{ str_replace(' Inning', '', $inning) }}</div>
        <flux:icon.caret-down variant="micro" class="dark:text-muted"/>
    @else
        <flux:icon.caret-up variant="micro" class="dark:text-muted"/>
        <div class="-ml-1 text-gray-800 dark:text-muted">{{ str_replace(' Inning', '', $inning) }}</div>
        <flux:icon.caret-down-fill variant="micro" class="text-black dark:text-cyan-400"/>
    @endif
</div>