<x-app-layout>
    <div class="p-4" x-data="{ openFiltro: false }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Alunos</h2>
            <div class="mb-4 flex gap-2">
                @if(auth()->user()->hasAnyRole([
                    App\Role::TREINADOR_GRAU_I->value,
                    App\Role::TREINADOR_GRAU_II->value,
                    App\Role::TREINADOR_GRAU_III->value,
                    App\Role::ARBITRATOR->value,
                    App\Role::SUPER_ADMIN->value,
                ]))
                    <a href="{{ route('student.create') }}" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md" style="background-color: #e62111">
                        ➕ Adicionar Aluno
                    </a>
            

                <button type="button" 
                        @click="openFiltro = !openFiltro" 
                        class="px-4 py-2 text-white rounded-md hover:bg-gray-700" style="background-color: #FF6600;">
                    <span x-show="!openFiltro">🔍 Abrir Filtros</span>
                    <span x-show="openFiltro">❌ Fechar Filtros</span>
                </button>
                @endif
            </div>
        </div>
        <!-- 🔘 Botões de ação -->
        

        @if(auth()->user()->hasAnyRole([
                App\Role::TREINADOR_GRAU_I->value,
                App\Role::TREINADOR_GRAU_II->value,
                App\Role::TREINADOR_GRAU_III->value,
                App\Role::ARBITRATOR->value,
                App\Role::SUPER_ADMIN->value,
            ]))
        <!-- 🔍 Form de Filtros -->
        <form method="GET" action="{{ route('student.index') }}" 
              x-show="openFiltro" x-transition 
              class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2 bg-gray-50 p-4 rounded-md shadow">
            
            <input type="text" name="nome" value="{{ request('nome') }}"
                placeholder="Buscar por nome..." class="px-3 py-2 border rounded-md">

            <input type="text" name="number_kak" value="{{ request('number_kak') }}"
                placeholder="Buscar por número KAK..." class="px-3 py-2 border rounded-md">

            <input type="text" name="clube" value="{{ request('clube') }}"
                placeholder="Buscar por clube..." class="px-3 py-2 border rounded-md">

            <select name="graduacao_id" class="px-3 py-2 border rounded-md">
                <option value="">-- Selecionar Graduação --</option>
                @foreach($graduacoes as $grad)
                    <option value="{{ $grad->id }}" {{ request('graduacao_id') == $grad->id ? 'selected' : '' }}>
                        {{ $grad->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-4 justify-self-end col-span-4 md:col-span-1">
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
        @endif

        <!-- 📋 Tabela de usuários e perfis -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50" style="background-color: #D9D9D9;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Número KAK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Clube</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Graduação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ações</th>
                        @if(auth()->user()->hasRole(\App\Role::SUPER_ADMIN))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Treinador</th>
                        @endif
                    </tr>
                </thead> 
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($alunos as $index => $profile)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap"> {{ $profile['number_kak'] }} - {{$profile["escalao"] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $profile['nome'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $profile['clube'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($profile['graduacao'] !== 'Sem graduação')
                                    @php
                                        $colorString = $profile['graduacao_color'];
                                        $colors = explode('_', $colorString);
                                        $colors = array_map(fn($c) => strtolower(trim($c)), $colors);
                                        $style = '';
                                        if(count($colors) > 1){
                                            $step = 100 / count($colors);
                                            $gradient = '';
                                            foreach($colors as $i => $color){
                                                $gradient .= "$color " . ($i * $step) . "%, $color " . (($i+1) * $step) . "%, ";
                                            }
                                            $gradient = rtrim($gradient, ', ');
                                            $style = "background: linear-gradient(to right, $gradient);";
                                        } else {
                                            $style = "background-color: {$colors[0]};";
                                        }
                                    @endphp
                                    <span style="display:inline-block; width:14px; height:14px; border-radius:50%;
                                                {{ $style }}
                                                border: 1px solid #000; margin-right:6px;"></span>
                                    {{ $profile['graduacao'] }}
                                @else
                                    Sem graduação
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="{{ route('student.graduations', $profile['profile_id']) }}" 
                                   class="text-green-600 hover:text-green-900" title="Graduações">
                                    <i class="fa-solid fa-user-ninja" aria-hidden="true"></i>
                                </a>    
                                <a href="{{ route('member.pdf', $profile['profile_id']) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Visualizar" target="_blank">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('student.edit', $profile['profile_id']) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Editar Perfil">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                
                                @if(auth()->user()->hasAnyRole([
                                    App\Role::TREINADOR_GRAU_I->value,
                                    App\Role::TREINADOR_GRAU_II->value,
                                    App\Role::TREINADOR_GRAU_III->value,
                                    App\Role::ARBITRATOR->value,
                                    App\Role::SUPER_ADMIN->value,
                                ]))
                                <form action="{{ route('student.destroy', $profile['profile_id']) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir Perfil">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                           <td>
                                <form action="{{ route('student.toggle-treinador', $profile['profile_id']) }}" method="POST" class="flex items-center gap-2 treinador-form">
                                    @csrf
                                    <input type="hidden" name="nome" value="{{ request('nome') }}">
                                    <input type="hidden" name="number_kak" value="{{ request('number_kak') }}">
                                    <input type="hidden" name="clube" value="{{ request('clube') }}">
                                    <input type="hidden" name="graduacao_id" value="{{ request('graduacao_id') }}">

                                    <select name="grau" class="border rounded-md text-sm p-1 treinador-select">
                                        <option value="no_rule" {{ !$profile['user']->hasAnyRole([
                                            \App\Role::TREINADOR_GRAU_I->value,
                                            \App\Role::TREINADOR_GRAU_II->value,
                                            \App\Role::TREINADOR_GRAU_III->value
                                        ]) ? 'selected' : '' }}>No Grau</option>
                                        <option value="I" {{ $profile['user']->hasRole(\App\Role::TREINADOR_GRAU_I->value) ? 'selected' : '' }}>Grau I</option>
                                        <option value="II" {{ $profile['user']->hasRole(\App\Role::TREINADOR_GRAU_II->value) ? 'selected' : '' }}>Grau II</option>
                                        <option value="III" {{ $profile['user']->hasRole(\App\Role::TREINADOR_GRAU_III->value) ? 'selected' : '' }}>Grau III</option>
                                    </select>
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

    <script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.treinador-select').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form'); // pega o form pai
            form.submit(); // envia o POST automaticamente
        });
    });
});
</script>
</x-app-layout>
