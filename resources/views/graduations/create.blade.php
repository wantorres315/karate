<x-app-layout>
    <div class="p-6 max-w-xl mx-auto bg-white rounded-md shadow-md">
        <h2 class="text-xl font-semibold mb-4">➕ Adicionar Graduação</h2>
        <form method="POST" action="{{ route('graduations.store') }}">
            @include('graduations._form')
        </form>
    </div>
</x-app-layout>
