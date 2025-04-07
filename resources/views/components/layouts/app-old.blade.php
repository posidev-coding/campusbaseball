<x-layouts.app.sidebar :icon="$icon ?? null" :title="$title ?? null" font="{{ \Illuminate\Support\Facades\Cache::get('font', 'font-sans') }}" font-name="{{ \Illuminate\Support\Facades\Cache::get('font-name', 'Noto Sans') }}">
       
    <flux:main>
        <div class="-mx-4.5 -my-3 sm:-mx-1 sm:-my-1 md:-mx-0 md:-my-0">
            {{ $slot }}
        </div>
    </flux:main>

</x-layouts.app.sidebar>
