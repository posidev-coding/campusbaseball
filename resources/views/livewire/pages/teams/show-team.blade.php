<div>

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('teams') }}">Teams</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $team->location . ' ' . $team->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:header>{{ $team->location . ' ' . $team->name }}</flux:header>
</div>
