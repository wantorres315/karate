<x-app-layout>
    <div class="p-6 max-w-4xl mx-auto bg-white shadow rounded">
        <h2 class="text-xl font-semibold mb-4">➕ Criar Turma</h2>
        <form method="POST" action="{{ route('classes.store') }}">
            @csrf
            @include('classes._form', ['class' => new App\Models\Classe()])
        </form>
    </div>
</x-app-layout>
