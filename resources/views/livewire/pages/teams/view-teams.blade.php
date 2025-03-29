<div>
    <flux:header>Teams</flux:header>

    <ul>
        @foreach ($this->teams as $team)
            <li><a href="{{ route('team', $team->id) }}">{{ $team->location . ' ' . $team->name }}</a></li>
        @endforeach
    </ul>
</div>
