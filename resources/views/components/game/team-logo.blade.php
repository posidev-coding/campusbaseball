@props([
  'team', 
  'size' => 12,
  'whitelist' => 'w-4 w-5 w-6 w-7 w-8 w-9 w-10 w-12 w-14 w-16 w-18 w-20 w-24 h-4 h-5 h-6 h-7 h-8 h-9 h-10 h-12 h-14 h-16 h-18 h-20 h-24'
  ])

<div clas="flex">
  <img src="{{ $team->logo }}" alt="{{ $team->abbreviation }}" class="w-{{ $size }} h-{{ $size }} flex dark:hidden" />
  <img src="{{ $team->darkLogo }}" alt="{{ $team->abbreviation }}" class="w-{{ $size }} h-{{ $size }} hidden dark:flex" />
</div>
