<x-app-layout>
    <div class="p-4" x-data="{ openFiltro: false }">
        
        <!-- 🔘 Botões de ação -->
        <div class="mb-4 flex gap-2">
            <a href="{{ route('student.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md" style="background-color: #e62111">
                ➕ Adicionar Aluno
            </a>

            <button type="button" 
                    @click="openFiltro = !openFiltro" 
                    class="px-4 py-2  text-white rounded-md hover:bg-gray-700" style="background-color: #FF6600;">
                <span x-show="!openFiltro">🔍 Abrir Filtros</span>
                <span x-show="openFiltro">❌ Fechar Filtros</span>
            </button>
        </div>

        <!-- 🔍 Form de Filtros (toggle) -->
        <form method="GET" action="{{ route('student.index') }}" 
              x-show="openFiltro" x-transition 
              class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2 bg-gray-50 p-4 rounded-md shadow">
            
            <input type="text" name="nome" value="{{ request('nome') }}"
                placeholder="Buscar por nome..."
                class="px-3 py-2 border rounded-md">

            <input type="text" name="number_kak" value="{{ request('number_kak') }}"
                placeholder="Buscar por número KAK..."
                class="px-3 py-2 border rounded-md">

            <input type="text" name="clube" value="{{ request('clube') }}"
                placeholder="Buscar por clube..."
                class="px-3 py-2 border rounded-md">

            <div class="flex gap-2 justify-self-end">
                <button type="submit"
                    class="px-4 py-2 text-white rounded-md hover:bg-blue-700"
                    style="background-color: #E62111;">
                    Filtrar
                </button>
                <a href="{{ route('student.index') }}"
                    class="px-4 py-2 text-gray-800 rounded-md hover:bg-gray-400"
                    style="background-color: #D9D9D9;">
                    Limpar
                </a>
            </div>
        </form>

        <!-- 📋 Tabela de alunos -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50" style="background-color: #D9D9D9;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Clube</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Número KAK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Graduação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Data da Graduação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($alunos as $aluno)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $loop->iteration + ($alunos->currentPage() - 1) * $alunos->perPage() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $aluno['nome'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $aluno['clube'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $aluno['number_kak'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                 @if($aluno['graduacao'] !== 'Sem graduação')
                                    <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background-color: {{ $aluno['graduacao_color'] }}; border: 1px solid #000; margin-right:6px;"></span>
                                    {{ $aluno['graduacao'] }}
                                @else
                                    Sem graduação
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $aluno['graduacao_data'] ? \Carbon\Carbon::parse($aluno['graduacao_data'])->format('d/m/Y') : '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="{{ route('student.graduations', $aluno['id']) }}" 
                                   class="text-green-600 hover:text-green-900" title="Graduações">
                                    <i class="fa-solid fa-user-ninja" aria-hidden="true"></i>
                                </a>    
                                <a href="{{ route('member.pdf', $aluno['id']) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Visualizar" target="_blank">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('student.edit', $aluno['id']) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Editar">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('student.destroy', $aluno['id']) }}" method="POST" class="inline">
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

        <!-- 📌 Paginação -->
        <div class="mt-4 flex justify-center">
            {{ $alunos->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
