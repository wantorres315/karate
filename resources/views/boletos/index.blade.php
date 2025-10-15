<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Boletos - {{ $mes }}/{{ $ano }}</h2>
            <form method="POST" action="{{ route('boletos.gerar') }}" class="flex items-center gap-2">
                @csrf
                <label class="font-semibold">Data de vencimento:</label>
                <input type="date" name="data_vencimento" value="{{ date('Y-m-d') }}"
                       class="border rounded px-2 py-1">
                <input type="hidden" name="mes" value="{{ $mes }}">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">➕ Gerar Boletos</button>
            </form>
        </div>

        <table class="min-w-full bg-white border rounded shadow">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Aluno</th>
                    <th class="px-4 py-2 text-left">Valor</th>
                    <th class="px-4 py-2 text-left">Vencimento</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boletos as $boleto)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $boleto->profile->name }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($boleto->valor, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            {{ $boleto->data_vencimento ? \Carbon\Carbon::parse($boleto->data_vencimento)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 capitalize">{{ $boleto->status_pagamento }}</td>
                        <td class="px-4 py-2 text-center flex flex-col items-center gap-2">
                            {{-- Download boleto --}}
                            @if($boleto->arquivo_boleto_url)
                                <a href="{{ route('boletos.download', $boleto) }}" class="text-blue-600 hover:underline">
                                    Baixar boleto
                                </a>
                            @endif

                            {{-- Marcar como pago --}}
                            @if($boleto->status_pagamento == 'nao_pago')
                                <form method="POST" action="{{ route('boletos.pagar', $boleto) }}">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                        Marcar como pago
                                    </button>
                                </form>
                            @endif

                            {{-- Upload comprovante --}}
                            <form method="POST" action="{{ route('boletos.comprovante', $boleto) }}" enctype="multipart/form-data" class="flex gap-2">
                                @csrf
                                <input type="file" name="comprovante" class="border rounded px-1 py-1">
                                <button type="submit" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Enviar comprovante
                                </button>
                            </form>

                            {{-- Download comprovante --}}
                            @if($boleto->arquivo_comprovante_url)
                                <a href="{{ route('boletos.comprovante.download', $boleto) }}" class="text-blue-600 hover:underline">
                                    Download comprovante
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border px-4 py-2 text-center">Nenhum boleto encontrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginação --}}
        <div class="mt-4">{{ $boletos->links() }}</div>
    </div>
</x-app-layout>
