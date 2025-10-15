<x-app-layout>
    <div class="p-6 max-w-4xl mx-auto bg-white shadow rounded">
        <h2 class="text-xl font-semibold mb-4">✏️ Editar Turma</h2>
        <form method="POST" action="{{ route('classes.update', $classe) }}">
            @csrf
            @method('PUT')
            @include('classes._form', ['class' => $classe])
        </form>
    </div>
</x-app-layout>
