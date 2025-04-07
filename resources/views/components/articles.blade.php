<div class="mx-auto lg:grid lg:grid-cols-4 lg:gap-4">

    
    <!-- Stories -->
    <div class="lg:col-span-3">
  
        {{-- <div class="text-lg font-bold text-gray-600 mb-1">Articles</div> --}}
  
        {{-- @foreach ($stories as $story) --}}
  
        <div class="flex flex-col prose max-w-3xl">
            <h2 class="dark:text-light">{{ $article->headline }}</h2>
            <p class="text-muted text-sm">{{ \Carbon\Carbon::parse($article->published)->shiftTimeZone('UTC')->setTimeZone('America/New_York')->format('F jS, Y') }}</p>
            <div class="dark:text-light">{!! $article->story !!}</div>
        </div>
  
        {{-- @endforeach --}}
  
    </div>

    <div class="lg:col-span-1">
  
        <div class="text-lg font-bold text-gray-600 mb-1">Highlights</div>

        <div class="flex flex-col space-y-4">

        @foreach ($highlights as $highlight)
  
            <div class="flex flex-col bg-card border dark:border-muted rounded-lg max-w-4xl grow">
                <div class="flex-shrink-0">
                    {{-- <img class="w-full object-cover object-top h-60" src="{{ $highlight->image }}" alt=""> --}}
                    <div class="iframe-wrapper">
                        <iframe class="iframe" src="https://www.espn.com/core/video/iframe/_/id/{{ $highlight->id }}/endcard/false" allowfullscreen frameborder="0"></iframe>
                    </div>
                </div>
                <div class="flex-1 p-2 flex flex-col justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-gray-900 dark:text-light">
                            {{ $highlight->headline }}
                        </p>
                        {{-- <p class="mt-2 text-sm text-gray-500">
                            {{ $highlight->description }}
                        </p> --}}
                    </div>
                    <div class="mt-1 flex items-center">
  
                        <div class="ml-3">
                            <div class="flex space-x-1 text-sm text-gray-500">
                                  {{ \Carbon\Carbon::parse($highlight->published)->shiftTimeZone('UTC')->setTimeZone('America/New_York')->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
  
        @endforeach
        </div>

    </div>
  
  </div>