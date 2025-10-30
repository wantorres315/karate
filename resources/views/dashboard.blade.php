<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Observação: passe dados reais do controller (ex: $account, $invoices, $students, $memberships, $activities) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Top Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 flex items-center space-x-4">
                <div class="bg-blue-500 text-white rounded-full p-3">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Conta</p>
                    <p class="text-lg font-semibold">{{ $account['name'] ?? 'Seu Dojo' }}</p>
                    <p class="text-xs text-gray-400">{{ $account['plan'] ?? 'Plano: Básico' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 flex items-center space-x-4">
                <div class="bg-green-500 text-white rounded-full p-3">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Alunos Ativos</p>
                    <p class="text-lg font-semibold">{{ $countStudentsActive ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Total de perfis ativos</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 flex items-center space-x-4">
                <div class="bg-yellow-500 text-white rounded-full p-3">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Próxima Aula</p>
                    <p class="text-lg font-semibold">{{ $nextClass ?? 'Sem agendamento' }}</p>
                    <p class="text-xs text-gray-400">Data / Hora</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 flex items-center space-x-4">
                <div class="bg-red-500 text-white rounded-full p-3">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Boletos Pendentes</p>
                    <p class="text-lg font-semibold">{{ $invoicesPending ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Abertos / Vencidos</p>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left column: Students & Quick Actions -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Students table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-lg font-semibold">Alunos recentes</h3>
                        <div class="text-sm text-gray-500">Mostrando 10</div>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-gray-500 text-left">
                                <tr>
                                    <th class="py-2 px-3">Nome</th>
                                    <th class="py-2 px-3 hidden sm:table-cell">Status</th>
                                    <th class="py-2 px-3 hidden md:table-cell">Última Presença</th>
                                    <th class="py-2 px-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <!-- Exemplo: foreach $students como $s -->
                                @forelse($students ?? [] as $s)
                                <tr class="border-t">
                                    <td class="py-3 px-3">{{ $s->name }}</td>
                                    <td class="py-3 px-3 hidden sm:table-cell">
                                        <span class="px-2 py-1 rounded {{ $s->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $s->active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 hidden md:table-cell">{{ $s->last_attendance ?? '-' }}</td>
                                    <td class="py-3 px-3">
                                        <a href="{{ route('members.show', $s->id) }}" class="text-blue-600 hover:underline text-sm">Ver</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="py-4 px-3 text-gray-500" colspan="4">Nenhum aluno encontrado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t text-right">
                        <a href="{{ route('members.index') }}" class="text-sm text-blue-600 hover:underline">Ver todos os alunos</a>
                    </div>
                </div>

                <!-- Upcoming classes / schedule -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Próximas aulas</h3>
                        <a href="{{ route('classes.index') }}" class="text-sm text-blue-600 hover:underline">Ver agenda</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($upcomingClasses ?? [] as $c)
                        <div class="flex items-center justify-between p-3 rounded border">
                            <div>
                                <div class="font-medium">{{ $c->title }}</div>
                                <div class="text-xs text-gray-500">{{ $c->datetime }}</div>
                            </div>
                            <div class="text-sm text-gray-600">{{ $c->spots ?? '-' }} vagas</div>
                        </div>
                        @empty
                        <div class="text-gray-500">Nenhuma aula agendada.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent activity -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-semibold">Atividades recentes</h3>
                    <div class="mt-3 space-y-2 text-sm text-gray-600">
                        @forelse($activities ?? [] as $a)
                        <div class="flex items-start space-x-3">
                            <div class="text-gray-400 text-xs mt-1">
                                <i class="fa-solid fa-circle"></i>
                            </div>
                            <div>
                                <div class="font-medium">{{ $a->title }}</div>
                                <div class="text-xs text-gray-500">{{ $a->time }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-gray-500">Sem atividades recentes.</div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right column: Billing, Memberships, Quick Actions -->
            <div class="space-y-6">

                <!-- Billing / invoices -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Cobranças</h3>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Ver boletos</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($invoices ?? [] as $inv)
                        <div class="flex items-center justify-between p-3 border rounded">
                            <div>
                                <div class="font-medium">{{ $inv->reference }}</div>
                                <div class="text-xs text-gray-500">Venc: {{ $inv->due_date }}</div>
                            </div>
                            <div class="text-sm">
                                <span class="px-2 py-1 rounded {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-gray-500">Sem cobranças recentes.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Memberships / plans -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Planos / Matrículas</h3>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Gerenciar</a>
                    </div>

                    <div class="mt-4">
                        @forelse($memberships ?? [] as $m)
                        <div class="flex items-center justify-between p-3 border rounded mb-2">
                            <div>
                                <div class="font-medium">{{ $m->name }}</div>
                                <div class="text-xs text-gray-500">{{ $m->student_name }}</div>
                            </div>
                            <div class="text-sm text-gray-600">{{ $m->status }}</div>
                        </div>
                        @empty
                        <div class="text-gray-500">Nenhuma matrícula ativa.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-semibold mb-2">Ações rápidas</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('members.create') }}" class="block text-center bg-blue-600 text-white py-2 rounded hover:opacity-90">Novo Aluno</a>
                        <a href="{{ route('classes.create') }}" class="block text-center bg-green-600 text-white py-2 rounded hover:opacity-90">Nova Aula</a>
                        <a href="#" class="block text-center bg-yellow-500 text-white py-2 rounded hover:opacity-90">Gerar Boleto</a>
                        <a href="#" class="block text-center bg-gray-700 text-white py-2 rounded hover:opacity-90">Relatórios</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
