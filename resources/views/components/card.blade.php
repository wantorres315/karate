<div {{ $attributes->merge(['class' => 'bg-gray-50 p-4 rounded shadow']) }}>
    @if(isset($title))
        <h3 class="font-semibold text-lg mb-3 text-gray-700">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>