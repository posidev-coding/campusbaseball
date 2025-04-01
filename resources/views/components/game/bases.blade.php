@props([
  'runners' => [], 
  'size',
  'whitelist' => 'border-4 mb-[8px] border-5 mb-[10px] border-6 mb-[12px] border-7 mb-[14px] border-8 mb-[16px] border-9 mb-[18px] border-10 mb-[20px]',
  'outs'
  ])
<div class="flex flex-col justify-items-center">
  <div class="flex flex-row-reverse relative justify-center">
      <div class="diamond first-base mb-0 border-{{ $size }} {{ in_array('onFirst', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
      <div class="diamond second-base mb-[{{ $size * 2 }}px] border-{{ $size }} {{ in_array('onSecond', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
      <div class="diamond third-base mb-0 border-{{ $size }} {{ in_array('onThird', $runners) ? 'border-blue-600 dark:border-yellow-300' : 'dark:border-gray-500' }}"></div>
  </div>

  @isset($outs)

    <div @class([
      'flex justify-center space-x-1 mt-1.5 mb-0 p-0',
      'mt-2' => $size > 4,
      'mt-2.5' => $size > 6,
      'mt-3' => $size > 8,
      'mt-3.5' => $size > 9,
      'space-x-1.5' => $size > 7,
      'space-x-2' => $size > 8,
      'space-x-2.5' => $size > 9,
    ])>
      <div @class([
        'out border-not-out border-[2.5px]',
        'border-out' => $outs > 0,
        'border-[5px]' => $size > 9,
        'border-[4.5px]' => $size > 7,
        'border-[4px]' => $size > 6,
        'border-[3.5px]' => $size > 5,
        'border-[3px]' => $size > 4
      ])></div>
      <div @class([
        'out border-not-out border-[2.5px]',
        'border-out' => $outs > 1,
        'border-[5px]' => $size > 9,
        'border-[4.5px]' => $size > 7,
        'border-[4px]' => $size > 6,
        'border-[3.5px]' => $size > 5,
        'border-[3px]' => $size > 4
      ])></div>
      <div @class([
        'out border-not-out border-[2.5px]',
        'out border-not-out border-[2.5px]',
        'border-out' => $outs > 2,
        'border-[5px]' => $size > 9,
        'border-[4.5px]' => $size > 7,
        'border-[4px]' => $size > 6,
        'border-[3.5px]' => $size > 5,
        'border-[3px]' => $size > 4
      ])></div>
    </div>
  @endisset

</div>