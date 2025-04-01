@props(['runners' => [], 'size' => 6])

{{-- <div class="BaseballBases"> --}}
  <div class="flex flex-row-reverse relative justify-center">
      <div class="diamond first-base mb-0 border border-{{ $size }} {{ in_array('onFirst', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
      <div class="diamond second-base border border-{{ $size }} mb-[{{ $size * 2 }}px] {{ in_array('onSecond', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
      <div class="diamond third-base mb-0 border-{{ $size }} {{ in_array('onThird', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
  </div>
{{-- </div> --}}