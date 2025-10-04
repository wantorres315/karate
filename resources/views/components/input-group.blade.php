@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'type' => 'text',
])

<div class="flex flex-col space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-900 dark:text-gray-100',
        ]) }}
    >
</div>
