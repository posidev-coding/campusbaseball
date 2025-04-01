@props(['team', 'size' => 12])

<div clas="flex">
  <img src="{{ $team->logo }}" alt="{{ $team->abbreviation }}" class="w-{{ $size }} h-{{ $size }} flex dark:hidden" />
  <img src="{{ $team->darkLogo }}" alt="{{ $team->abbreviation }}" class="w-{{ $size }} h-{{ $size }} hidden dark:flex" />
</div>
