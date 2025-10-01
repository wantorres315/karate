<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Graduações de {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERTAS --}}
            @if(session('success'))
                <div class="mb-4 p-4 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- LISTA --}}
            <div class="mb-4 p-4 rounded bg-green-100 text-green-800">
                    {{ $user->name }}
                </div>
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 border">Data</th>
                            <th class="px-3 py-2 border">Graduação</th>
                            <th class="px-3 py-2 border">Kihon</th>
                            <th class="px-3 py-2 border">Kata</th>
                            <th class="px-3 py-2 border">Kumite</th>
                            <th class="px-3 py-2 border">Local</th>
                            <th class="px-3 py-2 border">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($graduacoes as $g)
                            <tr>
                                <td class="px-3 py-2 border">{{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 border">
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background-color:{{ $g->graduation->color ?? '#ccc' }};border:1px solid #000;margin-right:6px;"></span>
                                    {{ $g->graduation->name }}
                                </td>
                                <td class="px-3 py-2 border">{{ $g->kihon ?? '-' }}</td>
                                <td class="px-3 py-2 border">{{ $g->kata ?? '-' }}</td>
                                <td class="px-3 py-2 border">{{ $g->kumite ?? '-' }}</td>
                                <td class="px-3 py-2 border">{{ $g->location ?? '-' }}</td>
                                <td class="px-3 py-2 border">
                                    <form action="{{ route('student.removeGraduation', [$user, $g]) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-2 text-center">Nenhuma graduação cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FORM --}}
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="font-semibold mb-3">Adicionar Graduação</h3>
                <form action="{{ route('student.addGraduation', $user->id) }}" method="POST">
                    @csrf

                    {{-- PRIMEIRA LINHA: Graduação, Data, Valor --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block mb-1">Graduação</label>
                            <select name="graduation_id" class="w-full border rounded px-2 py-1" required>
                                <option value="">-- Selecione --</option>
                                @foreach($todasGraduacoes as $grad)
                                    <option value="{{ $grad->id }}">{{ $grad->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1">Data</label>
                            <input type="date" name="date" class="w-full border rounded px-2 py-1" required>
                        </div>
                        
                    </div>

                    {{-- SEGUNDA LINHA: Kihon, Kata, Kumite --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block mb-1">Valor</label>
                            <input type="number" step="0.01" name="value" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block mb-1">Kihon</label>
                            <input type="text" name="kihon" class="w-full border rounded px-2 py-1">
                        </div>
                        
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block mb-1">Kata</label>
                            <input type="text" name="kata" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="block mb-1">Kumite</label>
                            <input type="text" name="kumite" class="w-full border rounded px-2 py-1">
                        </div>
                    </div>
                    {{-- TERCEIRA LINHA: Local --}}
                    <div class="mb-3">
                        <label class="block mb-1">Local</label>
                        <input type="text" name="location" class="w-full border rounded px-2 py-1">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Adicionar
                    </button>
                </form>
            </div>


        </div>
    </div>
</x-app-layout>
