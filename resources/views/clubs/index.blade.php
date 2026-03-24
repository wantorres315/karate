<x-app-layout>
    <div class="max-w-7xl mx-auto p-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h1 class="text-2xl font-semibold">Clubes</h1>
            <a href="{{ route('clubs.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                <i class="fa fa-plus"></i>
                <span>Adicionar Clube</span>
            </a>
        </div>

        <!-- Lista de clubes -->
        <div class="bg-white rounded-lg shadow">
            <div class="divide-y">
                @forelse($clubs as $club)
                <div class="flex items-center justify-between px-4 py-4 min-h-20 hover:bg-gray-50">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <!-- Logo -->
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center shrink-0 overflow-hidden">
                            @if($club->logo)
                                <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-600 font-semibold">{{ mb_substr($club->name, 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="leading-relaxed min-w-0 flex-1">
                            <div class="font-medium truncate">{{ $club->name }}</div>
                            <div class="text-xs text-gray-500">
                                <span>{{ $club->acronym }}</span> •
                                <span>{{ $club->email }}</span> •
                                <span>{{ $club->cell_number ?? $club->phone_number }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 flex-none">
                        <!-- Status -->
                        <div class="w-24 flex justify-end">
                            @if($club->status === 'active')
                                <span class="px-2 py-1 text-xs rounded-full text-white bg-green-600">Ativo</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full text-white bg-red-600">Inativo</span>
                            @endif
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center gap-3 justify-end w-28">
                           
                            <a href="{{ route('clubs.edit', ['club' => $club->id, 'page' => request('page')]) }}" 
                               title="Editar" class="text-blue-600 hover:text-blue-800">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form action="{{ route('clubs.destroy', $club->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este clube?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    Nenhum clube encontrado.
                </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($clubs->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $clubs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
