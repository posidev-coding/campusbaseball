@php $attributes = $unescapedForwardedAttributes ?? $attributes; @endphp

@props([
    'variant' => 'outline',
])

@php
    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
            'tiny' => '[:where(&)]:size-3',
        },
    );
@endphp

{{-- Your SVG code here: --}}
<svg 
  {{ $attributes->class($classes) }} 
  data-flux-icon 
  aria-hidden="true" 
  xmlns="http://www.w3.org/2000/svg" 
  fill="currentColor"
  viewBox="0 0 16 16">
    <path
        d="M3.204 11h9.592L8 5.519zm-.753-.659 4.796-5.48a1 1 0 0 1 1.506 0l4.796 5.48c.566.647.106 1.659-.753 1.659H3.204a1 1 0 0 1-.753-1.659" />
</svg>
