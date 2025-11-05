<x-app-layout>
    <div class="p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold">Turmas</h2>
            <div class="flex items-center gap-2 md:hidden">
                <a href="{{ route('classes.create') }}" class="px-4 py-2 bg-red-600 text-white rounded">➕ Nova Turma</a>
            </div>
        </div>

        {{-- Lista Mobile (cards) --}}
        <div class="md:hidden space-y-3">
            @foreach($classes as $class)
                @php
                    $daysMap = [
                        'monday' => 'Segunda-feira',
                        'tuesday' => 'Terça-feira',
                        'wednesday' => 'Quarta-feira',
                        'thursday' => 'Quinta-feira',
                        'friday' => 'Sexta-feira',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                    ];
                    $weekDaysRaw = is_array($class->week_days)
                        ? $class->week_days
                        : json_decode($class->week_days, true) ?? [];
                    $weekDays = [];
                    foreach ($weekDaysRaw as $day) { $weekDays[] = $daysMap[$day] ?? $day; }
                @endphp

                <div class="bg-white border rounded shadow p-4">
                    <div class="flex justify-between gap-3">
                        <div>
                            <div class="font-semibold">{{ $class->name }}</div>
                            <div class="text-sm text-gray-600">{{ $class->club->name ?? '—' }}</div>
                        </div>
                        <div class="text-right text-sm text-gray-700">
                            {{ $class->start_time }} - {{ $class->end_time }}
                        </div>
                    </div>

                    <div class="mt-2 text-sm">
                        <div><span class="font-medium">Instrutores:</span><br>{!! implode('<br>', $class->instructors->pluck('name')->toArray()) ?? '—' !!}</div>
                        <div class="mt-1"><span class="font-medium">Alunos:</span> {{ $class->students->count() }}</div>
                        <div class="mt-1">
                            <span class="font-medium">Dias:</span><br>
                            {!! !empty($weekDays) ? implode('<br> ', $weekDays) : '—' !!}
                        </div>
                    </div>

                    <div class="mt-3 flex items-center gap-4">
                        <a href="{{ route('classes.pdf', $class->id) }}" class="text-black-600" title="Download PDF" aria-label="Download PDF">
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('classes.attendance', $class->id) }}" class="text-green-600" title="Presenças" aria-label="Presenças">
                            <i class="fa fa-calendar-check" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('classes.edit', $class) }}" class="text-blue-600" title="Editar" aria-label="Editar">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </a>
                        <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline ml-auto">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Excluir turma?')" class="text-red-600" title="Excluir" aria-label="Excluir">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tabela Desktop/Tablet --}}
        <div class="hidden md:block">
            {{-- Toolbar de filtros (similar ao members) --}}
            <form id="classesFiltersForm" method="GET" class="mb-3 flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Pesquisar turma, instrutor..."
                           class="w-full border rounded pl-9 pr-3 py-2">
                    <span class="absolute left-3 top-2.5 text-gray-500">
                        <i class="fa fa-search"></i>
                    </span>
                </div>

                @if(isset($clubs) && count($clubs))
                    <select name="club_id" class="border rounded py-2 px-3 bg-white">
                        <option value="">Todos os clubes</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ (string)request('club_id')===(string)$club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <button class="px-3 py-2 border rounded bg-white hover:bg-gray-50" type="submit">
                    Aplicar
                </button>

                <a href="{{ route('classes.index') }}" class="px-3 py-2 border rounded bg-white hover:bg-gray-50">Limpar</a>

                <a href="{{ route('classes.create') }}" class="ml-auto px-4 py-2 bg-red-600 text-white rounded">
                    ➕ Nova Turma
                </a>
            </form>

            <div class="overflow-x-auto bg-white border rounded shadow">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2 text-left w-[22%]">Nome / Clube</th>
                            <th class="px-4 py-2 text-left w-[18%]">Instrutores</th>
                            <th class="px-4 py-2 text-left w-[14%]">Horário</th>
                            <th class="px-4 py-2 text-left w-[22%]">Dias da Semana</th>
                            <th class="px-4 py-2 text-left w-[8%]">Alunos</th>
                            <th class="px-4 py-2 text-center w-[16%]">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($classes as $class)
                            @php
                                $daysMap = [
                                    'monday' => 'Segunda',
                                    'tuesday' => 'Terça',
                                    'wednesday' => 'Quarta',
                                    'thursday' => 'Quinta',
                                    'friday' => 'Sexta',
                                    'saturday' => 'Sáb',
                                    'sunday' => 'Dom',
                                ];
                                $weekDaysRaw = is_array($class->week_days)
                                    ? $class->week_days
                                    : (json_decode($class->week_days, true) ?? []);
                                $weekDays = [];
                                foreach ($weekDaysRaw as $day) { $weekDays[] = $daysMap[strtolower($day)] ?? ($daysMap[$day] ?? $day); }
                            @endphp
                            <tr class="odd:bg-gray-50">
                                <td class="px-4 py-2 align-top">
                                    <div class="font-medium">{{ $class->name }}</div>
                                    <div class="text-gray-600">{{ $class->club->name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-2 align-top">
                                    {!! $class->instructors->isNotEmpty()
                                        ? implode('<br>', $class->instructors->pluck('name')->toArray())
                                        : '—' !!}
                                </td>
                                <td class="px-4 py-2 align-top">
                                    {{ $class->start_time }} - {{ $class->end_time }}
                                </td>
                                <td class="px-4 py-2 align-top">
                                    @if(!empty($weekDays))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($weekDays as $d)
                                                <span class="px-2 py-0.5 rounded-full bg-gray-100 border text-gray-700">{{ $d }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2 align-top">
                                    {{ $class->students->count() }}
                                </td>
                                <td class="px-4 py-2 text-center align-top">
                                    <div class="flex items-center justify-center gap-2">
                                        <a target="_blank" href="{{ route('classes.pdf', $class->id) }}" class="px-2 py-1 border rounded hover:bg-gray-50" title="Download PDF" aria-label="Download PDF">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <a href="{{ route('classes.attendance', $class->id) }}" class="px-2 py-1 border rounded hover:bg-gray-50 text-green-700" title="Presenças" aria-label="Presenças">
                                            <i class="fa fa-calendar-check"></i>
                                        </a>
                                        <a href="{{ route('classes.edit', $class) }}" class="px-2 py-1 border rounded hover:bg-gray-50 text-blue-700" title="Editar" aria-label="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form action="{{ route('classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Excluir turma?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2 py-1 border rounded hover:bg-gray-50 text-red-700" title="Excluir" aria-label="Excluir">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $classes->appends(request()->query())->links() }}</div>
    </div>
</x-app-layout>

<script>
// Auto submit nos filtros ao mudar selects (mantém comportamento ágil)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('classesFiltersForm');
    if (!form) return;
    form.querySelectorAll('select').forEach(el => {
        el.addEventListener('change', () => form.submit());
    });
});
</script>
