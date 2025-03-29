@props(['title', 'description'])

<div class="flex w-full flex-col gap-2 text-center">
    <h1 class="text-xl font-medium dark:text-slate-200">{{ $title }}</h1>
    <p class="text-center text-sm dark:text-slate-400">{{ $description }}</p>
</div>
