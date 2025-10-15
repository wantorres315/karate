<x-app-layout>
    <div class="p-4" x-data="{ openFiltro: false }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Graduações</h2>
             <a href="{{ route('graduations.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md" style="background-color: #e62111">
                ➕ Adicionar Graduação
            </a>
        </div>

        <!-- 🔍 Filtros -->
        <form method="GET" action="{{ route('graduations.index') }}" 
              x-show="openFiltro" x-transition 
              class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2 bg-gray-50 p-4 rounded-md shadow">

            <input type="text" name="name" value="{{ request('name') }}"
                placeholder="Buscar por nome..."
                class="px-3 py-2 border rounded-md">

            <div class="flex gap-2 justify-self-end">
                <button type="submit"
                    class="px-4 py-2 text-white rounded-md hover:bg-blue-700"
                    style="background-color: #E62111;">
                    Filtrar
                </button>
                <a href="{{ route('graduations.index') }}"
                    class="px-4 py-2 text-gray-800 rounded-md hover:bg-gray-400"
                    style="background-color: #D9D9D9;">
                    Limpar
                </a>
            </div>
        </form>

        <!-- 📋 Tabela de graduações -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50" style="background-color: #D9D9D9;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Cor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($graduations as $graduation)
                        <tr class="hover:bg-gray-100">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $colors = explode('_', $graduation->color);
                                $colors = array_map(fn($c) => strtolower(trim($c)), $colors);
                                $count = count($colors);
                                $width = 100 / $count;
                                @endphp
                                
                                <span style="display:inline-block; width:18px; height:18px; border-radius:50%; 
                                             border:1px solid #000; overflow:hidden; vertical-align:middle;">
                                    @foreach($colors as $color)
                                    <span style="float:left; width:{{ $width }}%; height:100%; background-color:{{ $color }};"></span>
                                    @endforeach
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $graduation->name }}</td>
                           
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                
                                <a href="{{ route('graduations.edit', ['graduation' => $graduation->id, 'page' => request('page')]) }}">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                <form action="{{ route('graduations.destroy', $graduation->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir"
                                            onclick="return confirm('Tem certeza que deseja excluir esta graduação?')">
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
            {{ $graduations->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</x-app-layout>
