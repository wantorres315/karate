{{-- resources/views/components/dashboard/box.blade.php --}}
@props(['title'])

<div {{ $attributes->merge(['class' => 'flex-grow flex flex-col bg-white rounded-xl shadow p-4']) }}>
    <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
    {{ $slot }}
</div>
