<x-layouts.app.sidebar :icon="$icon ?? null" :title="$title ?? null" font="{{ \Illuminate\Support\Facades\Cache::get('font', 'font-sans') }}" font-name="{{ \Illuminate\Support\Facades\Cache::get('font-name', 'Noto Sans') }}">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
