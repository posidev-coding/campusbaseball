<div>
    <flux:header>Conferences</flux:header>

    <ul>
        @foreach ($this->conferences as $conf)
            <li><a href="{{ route('conference', $conf->id) }}">{{ $conf->name }}</a></li>
        @endforeach
    </ul>
</div>
