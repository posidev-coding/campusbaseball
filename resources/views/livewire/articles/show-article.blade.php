<div class="flex justify-center">

    <div class="flex flex-col prose max-w-3xl">
        <h2>{{ $article->headline }}</h2>
        <p class="text-muted text-sm">{{ \Carbon\Carbon::parse($article->published)->shiftTimeZone('UTC')->setTimeZone('America/New_York')->format('F jS, Y') }}</p>
        {!! $article->story !!}
    </div>

</div>