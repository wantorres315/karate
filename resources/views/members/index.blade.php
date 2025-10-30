<x-app-layout>
    <div class="max-w-7xl mx-auto p-4" x-data="{ openFiltro: false }">

        <!-- Header / ações -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h1 class="text-2xl font-semibold">Membros</h1>

            <div class="flex items-center gap-2">
                

                @if(auth()->user()->hasAnyRole([
                    App\Role::TREINADOR_GRAU_I->value,
                    App\Role::TREINADOR_GRAU_II->value,
                    App\Role::TREINADOR_GRAU_III->value,
                    App\Role::ARBITRATOR->value,
                    App\Role::SUPER_ADMIN->value,
                ]))
                <a href="{{ route('members.create') }}" class="px-4 py-2 rounded-md bg-rose-600 text-white">➕ Novo</a>
                
                @endif
            </div>
        </div>

        <!-- Conteúdo principal: lista + resumo lateral -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Lista (col-span 3) -->
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- cabeça da lista -->
                    <div class="flex items-center justify-between px-4 py-3 border-b">
                        <div class="text-sm text-gray-600">Lista de Membros</div>
                        <div class="text-sm text-gray-500">Total: <strong>{{ $alunos->total() }}</strong></div>
                    </div>

                    <!-- linhas com estilo "card-like" (padrão parecido com Dojo Expert) -->
                    <div class="divide-y">
                        @forelse($alunos as $profile)
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <!-- avatar placeholder -->
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
                                    {{ mb_substr($profile['name'],0,1) }}
                                </div>

                                <div>
                                    <div class="font-medium">{{ $profile['name'] }}</div>
                                    <div class="text-xs text-gray-500">
                                        KAK: {{ $profile['number_kak'] ?? '-' }} • {{ $profile['clube'] ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <!-- graduação (círculo colorido) -->
                                <div class="flex items-center gap-2">
                                    @if($profile->lastGraduation)
                                        @php
                                            $colorString = $profile->lastGraduation->graduation->color ?? 'gray';
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
                                        <div class="w-3 h-3 rounded-full border" style="{{ $style }}"></div>
                                        <div class="text-sm text-gray-700">{{ $profile->lastGraduation->graduation->name }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">Sem graduação</div>
                                    @endif
                                </div>

                                <!-- ações -->
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('members.graduations', $profile['id']) }}" title="Graduações" class="text-green-600 hover:text-green-800">
                                        <i class="fa-solid fa-user-ninja"></i>
                                    </a>
                                    <a href="{{ route('member.pdf', $profile['id']) }}" target="_blank" title="Visualizar" class="text-blue-600 hover:text-blue-800">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $profile['id']) }}" title="Editar" class="text-gray-600 hover:text-gray-800">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    @if(auth()->user()->hasAnyRole([
                                        App\Role::TREINADOR_GRAU_I->value,
                                        App\Role::TREINADOR_GRAU_II->value,
                                        App\Role::TREINADOR_GRAU_III->value,
                                        App\Role::ARBITRATOR->value,
                                        App\Role::SUPER_ADMIN->value,
                                    ]))
                                    <form action="{{ route('members.destroy', $profile['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-6 text-center text-gray-500">Nenhum membro encontrado.</div>
                        @endforelse
                    </div>

                    <!-- paginação -->
                    <div class="px-4 py-3 border-t flex items-center justify-between">
                        <div class="text-sm text-gray-500">Mostrando {{ $alunos->firstItem() ?? 0 }} - {{ $alunos->lastItem() ?? $alunos->count() }} de {{ $alunos->total() }}</div>
                        <div>
                            {{ $alunos->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo lateral -->
            <aside class="space-y-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Resumo</div>
                            <div class="text-xl font-semibold">{{ $alunos->total() }}</div>
                        </div>
                        <div class="text-sm text-gray-500 text-right">
                            <div>Ativos: <strong>{{ $alunos->where('active',1)->count() ?? 0 }}</strong></div>
                            <div>Inativos: <strong>{{ $alunos->where('active',0)->count() ?? 0 }}</strong></div>
                        </div>
                    </div>
                </div>

                
            </aside>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.treinador-select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                form.submit();
            });
        });
    });
    </script>
</x-app-layout>
