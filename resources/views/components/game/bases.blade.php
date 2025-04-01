@props([
  'runners' => [], 
  'size',
  'whitelist' => 'border-4 mb-[8px] border-5 mb-[10px] border-6 mb-[12px] border-7 mb-[14px] border-8 mb-[16px] border-9 mb-[18px] border-10 mb-[20px]'
  ])

<div class="flex flex-row-reverse relative justify-center">
    <div class="diamond first-base mb-0 border-{{ $size }} {{ in_array('onFirst', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
    <div class="diamond second-base mb-[{{ $size * 2 }}px] border-{{ $size }} {{ in_array('onSecond', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
    <div class="diamond third-base mb-0 border-{{ $size }} {{ in_array('onThird', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
</div>