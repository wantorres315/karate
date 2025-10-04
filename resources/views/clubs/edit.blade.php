<x-app-layout>
    <div class="p-6 max-w-xl mx-auto bg-white rounded-md shadow-md">
        <h2 class="text-xl font-semibold mb-4">✏️ Editar Clube</h2>
        <form method="POST" action="{{ route('clubs.update', $club->id) }}">
            @csrf
            @method('PUT')

            {{-- Mantém página e filtros --}}
            <input type="hidden" name="page" value="{{ request('page', 1) }}">
            <input type="hidden" name="name" value="{{ request('name') }}">
            <input type="hidden" name="sigla" value="{{ request('sigla') }}">

            @include('clubs._form')
        </form>
    </div>
</x-app-layout>
