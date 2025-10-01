@props(['title', 'value', 'icon', 'color'])

<div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
  <div class="flex-auto p-4">
    <div class="flex flex-row -mx-3">
      <div class="flex-none w-2/3 max-w-full px-3">
        <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">{{ $title }}</p>
        <h5 class="mb-2 font-bold dark:text-white">{{ $value }}</h5>
      </div>
      <div class="px-3 text-right basis-1/3">
        <div class="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl {{ $color }}">
          {!! $icon !!}
        </div>
      </div>
    </div>
  </div>
</div>