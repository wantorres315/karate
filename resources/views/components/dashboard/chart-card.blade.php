@props(['title', 'canvasId'])

<div class="border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
  @if($title)
  <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-0">
    <h6 class="capitalize dark:text-white">{{ $title }}</h6>
  </div>
  @endif
  <div class="flex-auto p-4">
    <canvas id="{{ $canvasId }}" class="w-full h-64"></canvas>
  </div>
</div>