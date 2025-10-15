<x-app-layout>
    <div class="min-h-screen w-full bg-gray-100 flex items-center justify-center p-6">
        <div class="w-full max-w-6xl bg-white rounded-md shadow-md p-8">
            <h2 class="text-xl font-semibold mb-4">✏️ Editar Clube</h2>
        <form method="POST" action="{{ route('clubs.update', $club->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Mantém página e filtros --}}
                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                <input type="hidden" name="name" value="{{ request('name') }}">
                <input type="hidden" name="sigla" value="{{ request('sigla') }}">

                @include('clubs._form')
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold transition-colors">
                        Salvar
                    </button>
                    <a href="{{ route('clubs.index') }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md font-semibold transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
