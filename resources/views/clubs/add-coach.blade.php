<x-app-layout>
    <div class="min-h-screen w-full bg-gray-100 flex flex-col items-center p-6">
        <div class="w-full max-w-5xl bg-white rounded-md shadow-md p-8">
            <h2 class="text-2xl font-semibold mb-6">
                ➕ Adicionar Treinador ao Clube: <span class="text-red-600">{{ $club->name }}</span>
            </h2>

            {{-- MENSAGEM DE SUCESSO --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORMULÁRIO --}}
            <form method="POST" action="{{ route('clubs.storeCoach', $club) }}" class="w-full space-y-6">
                @csrf

                <div>
                    <label for="student_id" class="block text-gray-700 font-semibold mb-2">
                        Selecionar Estudante
                    </label>
                    <select name="student_id" id="student_id"
                            class="w-full  p-2 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="">Escolha um estudante</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold transition-colors">
                        Salvar
                    </button>
                    <a href="{{ route('clubs.index') }}"
                       class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md font-semibold transition-colors">
                        Voltar
                    </a>
                </div>
            </form>
        </div>

        {{-- LISTA DE TREINADORES --}}
        <div class="w-full max-w-5xl bg-white rounded-md shadow-md p-8 mt-6">
            <h3 class="text-xl font-semibold mb-4">👨‍🏫 Treinadores do Clube</h3>

            @if($coaches->isEmpty())
                <p class="text-gray-600">Nenhum treinador cadastrado ainda.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($coaches as $coach)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coach->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coach->user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('clubs.removeCoach', [$club, $coach]) }}" method="POST" onsubmit="return confirm('Remover este treinador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
