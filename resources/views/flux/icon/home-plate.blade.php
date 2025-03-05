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
        },
    );
@endphp

<svg {{ $attributes->class($classes) }} data-flux-icon aria-hidden="true" id="fi_15386843" viewBox="0 0 256 256"
    xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
    <path
        d="m236.9 132.82v-113.73a3.58 3.58 0 0 0 -2.22-3.32 3.66 3.66 0 0 0 -1.38-.28h-210.6a3.66 3.66 0 0 0 -1.38.28 3.58 3.58 0 0 0 -2.22 3.32v113.74a3.44 3.44 0 0 0 .26 1.31.17.17 0 0 0 0 .07 3.52 3.52 0 0 0 .71 1.06s0 .07.07.1l104 104.07c.07.07.16.1.23.16a3.87 3.87 0 0 0 .93.62 3.5 3.5 0 0 0 1.05.21c.11 0 .21.07.32.07a3.14 3.14 0 0 0 2.19-.81 3.53 3.53 0 0 0 .32-.21l106.57-104s0-.08.07-.11a3.45 3.45 0 0 0 .73-1.07 3.35 3.35 0 0 0 .35-1.48zm-210.6-105.04 8.35 8.36v87.86l-8.35 3.46zm100.57 182.22-85-85.08v-86.67h172.3v86.51zm89.39-178.95h-176.52l-8.35-8.36h193.22zm-178.86 99.58 85.83 85.89-.07 11.74-94.11-94.17zm93 85.93 88.12-86 8.36 3.52-96.55 94.27zm90.92-92.68v-87.74l8.35-8.36v99.62z"
        fill="#787878"></path>
</svg>
