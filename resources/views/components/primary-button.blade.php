<button {{ $attributes->merge([
    'class' => 'inline-flex items-center px-4 py-2 bg-brand-red text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest
                  hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
