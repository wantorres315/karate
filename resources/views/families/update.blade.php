<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Editar Família</h2>
    </x-slot>
    <div class="p-6">
        <form action="{{ route('familias.update', $family) }}" method="POST">
            @csrf @method('PUT')
            <div>
                <label>Nome:</label>
                <input type="text" name="name" value="{{ $family->name }}" class="border rounded w-full p-2" required>
            </div>
            <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">Atualizar</button>
        </form>
    </div>
</x-app-layout>