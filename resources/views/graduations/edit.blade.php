<x-app-layout>
    <div class="p-6 max-w-xl mx-auto bg-white rounded-md shadow-md">
        <h2 class="text-xl font-semibold mb-4">✏️ Editar Graduação</h2>
        <form method="POST" action="{{ route('graduations.update', $graduation->id) }}">
            @csrf
            @method('PUT')

            {{-- Input hidden para manter a página atual e filtros --}}
            <input type="hidden" name="page" value="{{ request('page', 1) }}">

            @include('graduations._form')
        </form>
    </div>
</x-app-layout>
