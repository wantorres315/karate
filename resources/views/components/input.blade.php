@props(['label', 'name', 'value' => '', 'type' => 'text', 'disabled' => false])
<div class="mb-2">
    <label class="block text-sm font-medium text-gray-700 mb-1" for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" 
        @if($disabled) disabled="disabled" @endif
        name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
        {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300']) }}>
</div>