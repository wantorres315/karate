{{-- resources/views/components/dashboard/summary-card.blade.php --}}
@props(['title', 'value', 'color' => 'gray'])

@php
    // Tailwind JIT exige classes literais ou safelist; ajuste conforme seu setup.
    $textClass = match($color) {
        'green'  => 'text-green-600',
        'blue'   => 'text-blue-600',
        'yellow' => 'text-yellow-600',
        'purple' => 'text-purple-600',
        default  => 'text-gray-600',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow p-4 w-full h-full']) }}>
    <h2 class="text-sm text-gray-500">{{ $title }}</h2>
    <p class="text-2xl font-bold {{ $textClass }}">{{ $value }}</p>
</div>
