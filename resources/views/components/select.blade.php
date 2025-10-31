@props(['label', 'name', 'options' => [], 'selected' => ''])
<div class="mb-2">
    <label class="block text-sm font-medium text-gray-700 mb-1" for="{{ $name }}">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300']) }}>
        @foreach($options as $option)
            <option value="{{ $option[0] }}" @if($selected == $option[0]) selected @endif>{{ $option[1] }}</option>
        @endforeach
    </select>
</div>