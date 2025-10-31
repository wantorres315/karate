@props([
    'label' => '',
    'name',
    'value' => '',
    'required' => false,
])
<div class="flex flex-col space-y-1">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <input
        type="date"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500']) }}
    >
</div>