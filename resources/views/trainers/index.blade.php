<x-app-layout>
    <div class="max-w-7xl mx-auto p-4" x-data="trainerPage()">

        <!-- Header / ações (mantido) -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h1 class="text-2xl font-semibold">Treinadores</h1>

            <div class="flex items-center gap-2">
                @if(auth()->user()->hasAnyRole([
                    App\Role::TREINADOR_GRAU_I->value,
                    App\Role::TREINADOR_GRAU_II->value,
                    App\Role::TREINADOR_GRAU_III->value,
                    App\Role::ARBITRATOR->value,
                    App\Role::SUPER_ADMIN->value,
                ]))
                    <button @click="openPromote()" type="button" class="px-4 py-2 rounded-md bg-rose-600 text-white">
                        ➕ Novo
                    </button>
                @endif
            </div>
        </div>

        <!-- Filtros (como antes) -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5 bg-white rounded shadow p-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nome, email ou clube" class="border rounded p-2">
            
            @isset($clubs)
                <select name="club_id" class="border rounded p-2">
                    <option value="">Todos os clubes</option>
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}" @selected(request('club_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            @endisset
            <div class="flex items-center gap-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full md:w-auto">Filtrar</button>
                <a href="{{ route('trainers.index') }}" class="px-3 py-2 text-sm border rounded w-full text-center md:w-auto">Limpar</a>
            </div>
        </form>

        <!-- Conteúdo principal: lista + resumo lateral -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Lista (col-span 3) -->
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- cabeça da lista -->
                    <div class="flex items-center justify-between px-4 py-3 border-b">
                        <div class="text-sm text-gray-600">Lista de Treinadores</div>
                        <div class="text-sm text-gray-500">Total: <strong>{{ $trainers->total() }}</strong></div>
                    </div>

                    <!-- linhas -->
                    <div id="trainersList" class="divide-y">
                        @forelse($trainers as $trainer)
                        <div class="flex items-center justify-between px-4 py-4 min-h-20 hover:bg-gray-50" id="row-{{ $trainer->id }}">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <!-- avatar -->
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold shrink-0">
                                    {{ mb_substr($trainer->name,0,1) }}
                                </div>

                                <div class="leading-relaxed min-w-0">
                                    <div class="font-medium name truncate">{{ $trainer->name }}</div>
                                    <div class="text-xs text-gray-500 meta">
                                        KAK: <span class="kak">{{ $trainer->number_kak ?? '-' }}</span> •
                                        Clubes:
                                        @php
                                            $primaryClub = $trainer->club;
                                            $allClubs = collect($primaryClub ? [$primaryClub] : [])
                                                ->merge($trainer->trainingClubs)
                                                ->unique('id');
                                        @endphp
                                        @if($allClubs->isEmpty())
                                            <span>—</span>
                                        @else
                                            <span class="clubs">
                                                {{ $allClubs->map(function($c) use ($primaryClub) {
                                                    $name = $c->name;
                                                    if ($primaryClub && $c->id === $primaryClub->id) {
                                                        $name .= ' (principal)';
                                                    }
                                                    return $name;
                                                })->implode(', ') }}
                                            </span>
                                        @endif
                                        • <span class="email">{{ $trainer->user->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 flex-none">
                                <!-- graduação (largura fixa) -->
                                <div class="flex items-center gap-2 justify-end w-56">
                                    @if($trainer->lastGraduation)
                                        @php
                                            $colorString = $trainer->lastGraduation->graduation->color ?? 'gray';
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
                                        <span class="w-3 h-3 rounded-full border inline-block shrink-0" style="{{ $style }}"></span>
                                        <span class="text-sm text-gray-700 truncate" title="{{ $trainer->lastGraduation->graduation->name }}">
                                            {{ $trainer->lastGraduation->graduation->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Sem graduação</span>
                                    @endif
                                </div>

                                <!-- ações (largura fixa) -->
                                <div class="flex items-center gap-3 justify-end w-28">
                                    <a href="{{ route('members.graduations', $trainer->id) }}" title="Graduações" class="text-green-600 hover:text-green-800">
                                        <i class="fa-solid fa-user-ninja"></i>
                                    </a>
                                    <a href="{{ route('member.pdf', $trainer->id) }}" target="_blank" title="Visualizar" class="text-blue-600 hover:text-blue-800">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasAnyRole([
                                        App\Role::TREINADOR_GRAU_I->value,
                                        App\Role::TREINADOR_GRAU_II->value,
                                        App\Role::TREINADOR_GRAU_III->value,
                                        App\Role::ARBITRATOR->value,
                                        App\Role::SUPER_ADMIN->value,
                                    ]))
                                    <button class="text-red-600 hover:text-red-900" data-demote="{{ $trainer->id }}" title="Excluir">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-6 text-center text-gray-500">Nenhum treinador encontrado.</div>
                        @endforelse
                    </div>

                    <!-- paginação -->
                    <div class="px-4 py-3 border-t flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $trainers->firstItem() ?? 0 }} - {{ $trainers->lastItem() ?? $trainers->count() }} de {{ $trainers->total() }}
                        </div>
                        <div>
                            {{ $trainers->appends(request()->query())->links() }}
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
                            <div class="text-xl font-semibold">{{ $trainers->total() }}</div>
                        </div>
                        <div class="text-sm text-gray-500 text-right">
                            <div>Ativos: <strong>{{ $trainers->where('active',1)->count() ?? 0 }}</strong></div>
                            <div>Inativos: <strong>{{ $trainers->where('active',0)->count() ?? 0 }}</strong></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Modal Promover -->
        <div x-show="showPromote" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-3">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-5">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold">Promover Membro a Treinador</h3>
                    <button @click="closePromote()" class="px-2 py-1 border rounded">X</button>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <input type="text" class="border rounded p-2" placeholder="Buscar por nome, email ou KAK" x-model="memberQuery" @input.debounce.300ms="searchMembers()">
                    <div class="max-h-64 overflow-y-auto border rounded">
                        <template x-if="members.length === 0">
                            <div class="p-3 text-sm text-gray-500">Digite para buscar membros...</div>
                        </template>
                        <ul>
                            <template x-for="m in members" :key="m.id">
                                <li class="flex items-center justify-between px-3 py-2 border-b">
                                    <div>
                                        <div class="font-medium" x-text="m.name"></div>
                                        <div class="text-xs text-gray-600" x-text="`${m.email ?? '-' } • ${m.number_kak ?? '-' } • ${m.club ?? '-'}`"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-2 py-1 rounded border" x-show="promoteForm.profile_id === m.id">Selecionado</span>
                                        <button @click="selectMember(m)" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded px-3 py-1" title="Selecionar">
                                            Selecionar
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Seleção de dojos onde dará aulas -->
                    <div class="border rounded p-3">
                        <div class="font-medium mb-2">Dojos onde poderá dar aulas</div>
                        <template x-if="!promoteForm.profile_id">
                            <div class="text-sm text-gray-500">Selecione um membro acima para habilitar.</div>
                        </template>

                        <div class="max-h-48 overflow-y-auto space-y-1" :class="!promoteForm.profile_id && 'opacity-50 pointer-events-none'">
                            <template x-for="c in clubs" :key="c.id">
                                <label class="flex items-center gap-2">
                                    <template x-if="promoteForm.primary_club_id === c.id">
                                        <input type="checkbox" checked disabled>
                                    </template>
                                    <template x-if="promoteForm.primary_club_id !== c.id">
                                        <input type="checkbox" :value="c.id" x-model.number="promoteForm.training_club_ids">
                                    </template>
                                    <span x-text="c.name"></span>
                                    <span class="text-xs text-gray-500" x-show="promoteForm.primary_club_id === c.id">(clube principal)</span>
                                </label>
                            </template>
                        </div>

                        <div class="flex gap-2 mt-2">
                            <button type="button" class="px-2 py-1 border rounded"
                                    :disabled="!promoteForm.profile_id"
                                    @click="promoteForm.training_club_ids = clubs.filter(c => c.id !== promoteForm.primary_club_id).map(c => c.id)">
                                Selecionar todos
                            </button>
                            <button type="button" class="px-2 py-1 border rounded"
                                    :disabled="!promoteForm.profile_id"
                                    @click="promoteForm.training_club_ids = []">
                                Limpar
                            </button>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">O clube principal está sempre autorizado.</div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="promote()" :disabled="!promoteForm.profile_id" class="px-4 py-2 bg-rose-600 text-white rounded disabled:opacity-50">
                            Promover
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-3">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-5">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold">Editar Treinador</h3>
                    <button @click="closeEdit()" class="px-2 py-1 border rounded">X</button>
                </div>

                <form @submit.prevent="saveEdit()">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-700">Nome</label>
                            <input type="text" class="w-full border rounded p-2" x-model="editForm.name">
                        </div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" x-model="editForm.is_treinador">
                            <span>Ativo como treinador</span>
                        </label>

                        <!-- Novos: dojos autorizados -->
                        <div>
                            <label class="text-sm text-gray-700">Dojos onde pode dar aulas</label>
                            <div class="max-h-48 overflow-y-auto border rounded p-2 mt-1">
                                <template x-for="c in clubs" :key="c.id">
                                    <label class="flex items-center gap-2 py-1">
                                        <template x-if="editForm.primary_club_id === c.id">
                                            <input type="checkbox" checked disabled>
                                        </template>
                                        <template x-if="editForm.primary_club_id !== c.id">
                                            <input type="checkbox" :value="c.id" x-model.number="editForm.training_club_ids">
                                        </template>
                                        <span x-text="c.name"></span>
                                        <span class="text-xs text-gray-500" x-show="editForm.primary_club_id === c.id">(clube principal)</span>
                                    </label>
                                </template>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button type="button" class="px-2 py-1 border rounded"
                                        @click="editForm.training_club_ids = clubs.filter(c => c.id !== editForm.primary_club_id).map(c => c.id)">
                                    Selecionar todos
                                </button>
                                <button type="button" class="px-2 py-1 border rounded"
                                        @click="editForm.training_club_ids = []">
                                    Limpar
                                </button>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">O clube principal está sempre autorizado.</div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="closeEdit()" class="px-3 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        // Lista de clubes disponível no controller (já usada no filtro)
        $clubsList = isset($clubs) ? $clubs->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values() : collect();
    @endphp
    <script>
        function trainerPage() {
            const token = '{{ csrf_token() }}';
            const TRAINERS_INDEX = @json(route('trainers.index'));
            const TRAINERS_MEMBERS = @json(route('trainers.members'));

            const listEl = () => document.getElementById('trainersList');

            const attachRowHandlers = (root = document) => {
                root.querySelectorAll('[data-edit]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = Number(btn.getAttribute('data-edit'));
                        const name = btn.getAttribute('data-name') ?? '';
                        const club = btn.getAttribute('data-club') ?? '';
                        const number_kak = btn.getAttribute('data-number_kak') ?? '';
                        const email = btn.getAttribute('data-email') ?? '';

                        const primary_club_id = Number(btn.getAttribute('data-primary_club_id') || 0);
                        let training_club_ids = [];
                        try {
                            training_club_ids = JSON.parse(btn.getAttribute('data-training_clubs') ?? '[]');
                        } catch(e) {
                            training_club_ids = (btn.getAttribute('data-training_clubs') || '')
                                .split(',').filter(Boolean).map(Number);
                        }

                        alpine.editForm = {
                            id, name, club, number_kak, email,
                            is_treinador: true,
                            primary_club_id,
                            training_club_ids
                        };
                        alpine.showEdit = true;
                    });
                });

                root.querySelectorAll('[data-demote]').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-demote');
                        if (!confirm('Remover este treinador?')) return;
                        const res = await fetch(`${TRAINERS_INDEX}/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            window.location.reload();
                        } else {
                            alert('Falha ao remover.');
                        }
                    });
                });
            };

            const alpine = {
                showPromote: false,
                showEdit: false,
                memberQuery: '',
                members: [],
                clubs: @json($clubsList),

                // Novo: formulário de promoção com clubes
                promoteForm: { profile_id: null, primary_club_id: null, training_club_ids: [] },

                openPromote() {
                    this.showPromote = true;
                    this.members = [];
                    this.memberQuery = '';
                    this.promoteForm = { profile_id: null, primary_club_id: null, training_club_ids: [] };
                },
                closePromote() { this.showPromote = false; },

                async searchMembers() {
                    const q = this.memberQuery.trim();
                    if (!q) { this.members = []; return; }
                    const url = new URL(TRAINERS_MEMBERS, window.location.origin);
                    url.searchParams.set('q', q);
                    @if(request()->filled('club_id'))
                        url.searchParams.set('club_id', @json((int) request('club_id')));
                    @endif
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                    if (res.ok) this.members = await res.json();
                },

                // Novo: selecionar membro e preparar clubes
                selectMember(m) {
                    this.promoteForm.profile_id = m.id;
                    this.promoteForm.primary_club_id = m.club_id ?? null; // garanta que a API retorna club_id
                    this.promoteForm.training_club_ids = [];
                },

                // Atualizado: promover com clubes selecionados
                async promote() {
                    const payload = {
                        profile_id: this.promoteForm.profile_id,
                        training_club_ids: this.promoteForm.training_club_ids
                    };
                    const res = await fetch(TRAINERS_INDEX, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) { alert('Falha ao promover.'); return; }
                    window.location.reload();
                },

                openEdit(trainer) {
                    this.editForm = { id: trainer.id, name: trainer.name, is_treinador: true, club: trainer.club, email: trainer.email, number_kak: trainer.number_kak };
                    this.showEdit = true;
                },
                closeEdit() { this.showEdit = false; },

                async saveEdit() {
                    const id = this.editForm.id;
                    const res = await fetch(`${TRAINERS_INDEX}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.editForm.name,
                            is_treinador: this.editForm.is_treinador ? 1 : 0,
                            training_club_ids: this.editForm.training_club_ids
                        })
                    });
                    if (!res.ok) { alert('Falha ao salvar.'); return; }
                    window.location.reload();
                },
            };

            // Handler de remoção também disponível pelo objeto (se usado em @click)
            alpine.demote = async (id) => {
                if (!confirm('Remover este treinador?')) return;
                const res = await fetch(`${TRAINERS_INDEX}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Falha ao remover.');
                }
            };

            requestAnimationFrame(() => attachRowHandlers(listEl() ?? document));
            return alpine;
        }
    </script>
    @endpush
</x-app-layout>