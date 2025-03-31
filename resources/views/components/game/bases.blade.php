@props(['runners' => [], 'small' => false])

<div class="BaseballBases">
  <div class="BaseballBases__Wrapper flex relative justify-center">
      <div @class([
          'diamond first-base border',
          'border-blue-600' => in_array('onFirst', $runners),
          'border-7' => !$small,
          'border-4' => $small,
          'mb-0' => $small
      ])></div>
      <div @class([
          'diamond second-base border',
          'border-blue-600' => in_array('onSecond', $runners),
          'border-7' => !$small,
          'border-4' => $small,
          'mb-[14px]' => !$small,
          'mb-[8px]' => $small,
      ])></div>
      <div @class([
          'diamond third-base border',
          'border-blue-600' => in_array('onThird', $runners),
          'border-7' => !$small,
          'border-4' => $small,
          'mb-0' => $small
      ])></div>
  </div>
</div>