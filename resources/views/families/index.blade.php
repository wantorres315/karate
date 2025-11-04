<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Famílias</h2>
    </x-slot>
    <div class="p-6">
        <!-- Formulário de criação rápida -->
        <form id="familiaForm" action="{{ route('familias.store') }}" method="POST" class="mb-6 flex gap-4 items-end bg-white p-4 rounded shadow">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="family_id" id="family_id">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome da Família</label>
                <input type="text" name="name" id="family_name" class="border rounded w-full p-2 mt-1" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email principal</label>
                <input type="email" name="email" id="family_email" class="border rounded w-full p-2 mt-1" required>
            </div>
            <button type="submit" id="formBtn" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Criar Família</button>
        </form>

        <!-- Tabela de famílias -->
        <div class="overflow-x-auto bg-white rounded shadow mt-6">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Nome</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Usuário Familiar</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($families as $family)
                    <tr class="hover:bg-gray-100 cursor-pointer"
                        onclick="editarFamilia({{ $family->id }}, '{{ addslashes($family->name) }}', '{{ addslashes($family->user->email ?? '') }}')">
                        <td class="px-6 py-2">{{ $family->id }}</td>
                        <td class="px-6 py-2">{{ $family->name ?? '-' }}</td>
                        <td class="px-6 py-2">{{ $family->user->name ?? '-' }} - {{$family->user->email}}</td>
                        <td class="px-6 py-2">
                            <form action="{{ route('familias.destroy', $family) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" onclick="return confirm('Deseja excluir esta família?')">Excluir</button>
                            </form>
                            <form action="{{ route('familias.resetSenha', $family) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" onclick="return confirm('Resetar a senha do usuário familiar?')">Resetar Senha</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($families->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-2 text-center text-gray-500">Nenhuma família cadastrada.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
function editarFamilia(id, name, email) {
    document.getElementById('family_id').value = id;
    document.getElementById('family_name').value = name;
    document.getElementById('family_email').value = email;
    document.getElementById('familiaForm').action = "{{ url('familias') }}/" + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formBtn').innerText = 'Salvar Alterações';
}
</script>