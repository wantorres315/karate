<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Nova Família</h2>
    </x-slot>
    <div class="p-6">
        <form action="{{ route('familias.store') }}" method="POST">
            @csrf
            <div>
                <label>Nome:</label>
                <input type="text" name="name" class="border rounded w-full p-2" required>
            </div>
            <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">Salvar</button>
        </form>
    </div>
</x-app-layout>