<x-layouts.auth.simple :title="$title ?? null" font="{{ \Illuminate\Support\Facades\Cache::get('font', 'font-sans') }}" font-name="{{ \Illuminate\Support\Facades\Cache::get('font-name', 'Noto Sans') }}">
    {{ $slot }}
</x-layouts.auth.simple>
