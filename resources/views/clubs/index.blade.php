<x-app-layout>
    <div class="p-4">
        <!-- Botões de ação -->
         <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Clubes</h2>
            <a href="{{ route('clubs.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-md">
                ➕ Adicionar Clube
            </a>
        </div>
        
        <!-- Tabela -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Sigla</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($clubs as $club)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo do Clube" class="mt-2 h-10">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $club->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $club->acronym }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $club->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $club->cell_number ?? $club->phone_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($club->status === 'active')
                                    <span class="px-2 py-1 rounded-full text-white bg-green-600">Ativo</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-white bg-red-600">Inativo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="{{ route('clubs.addCoach', ['club' => $club->id, 'page' => request('page')]) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Editar">
                                    <i class="fa-solid fa-user-ninja" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('clubs.edit', ['club' => $club->id, 'page' => request('page')]) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Editar">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('clubs.destroy', $club->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-4 flex justify-center">
            {{ $clubs->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
