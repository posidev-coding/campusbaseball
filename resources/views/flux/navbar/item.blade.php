@php $iconTrailing = $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant = $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware([ 'variant' ])

@props([
    'iconVariant' => 'outline',
    'iconTrailing' => null,
    'badgeColor' => null,
    'variant' => null,
    'iconDot' => null,
    'accent' => true,
    'square' => null,
    'badge' => null,
    'icon' => null,
])

@php
// Button should be a square if it has no text contents...
$square ??= $slot->isEmpty();

// Size-up icons in square/icon-only buttons...
$iconClasses = Flux::classes($square ? 'size-6' : 'size-5');

$classes = Flux::classes()
    ->add('px-3 h-8 flex items-center rounded-lg')
    ->add('relative') // This is here for the "active" bar at the bottom to be positioned correctly...
    ->add($square ? '' : 'px-2.5!')
    ->add('text-light')
    // Styles for when this link is the "current" one...
    ->add('data-current:after:absolute data-current:after:-bottom-3 data-current:after:inset-x-0 data-current:after:h-[3.5px]')
    ->add([
        '[--hover-fill:color-mix(in_oklab,_var(--color-accent-light),_transparent_90%)]',
    ])
    ->add(match ($accent) {
        true => [
            'hover:text-lightest',
            'data-current:text-white hover:data-current:text-white hover:bg-white/10 hover:data-current:bg-white/10',
            'data-current:after:bg-(--color-accent-light)',
        ],
        false => [
            'hover:text-slate-800 dark:hover:text-white',
            'data-current:text-slate-800 dark:data-current:text-slate-100 hover:bg-slate-100 dark:hover:bg-white/10',
            'data-current:after:bg-slate-800 dark:data-current:after:bg-white',
        ],
    })
    ;
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navbar-items>
    <?php if ($icon): ?>
        <div class="relative">
            <?php if (is_string($icon) && $icon !== ''): ?>
                <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
            <?php else: ?>
                {{ $icon }}
            <?php endif; ?>

            <?php if ($iconDot): ?>
                <div class="absolute top-[-2px] end-[-2px]">
                    <div class="size-[6px] rounded-full bg-slate-500 dark:bg-slate-400"></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($slot->isNotEmpty()): ?>
        <div class="{{ $icon ? 'ms-3' : '' }} flex-1 text-sm font-medium leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block" data-content>{{ $slot }}</div>
    <?php endif; ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
        <flux:icon :icon="$iconTrailing" variant="micro" class="size-4 ms-1" />
    <?php elseif ($iconTrailing): ?>
        {{ $iconTrailing }}
    <?php endif; ?>

    <?php if ($badge): ?>
        <flux:navbar.badge :color="$badgeColor" class="ms-2">{{ $badge }}</flux:navbar.badge>
    <?php endif; ?>
</flux:button-or-link>
