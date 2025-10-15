<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Turmas</h2>
            <a href="{{ route('classes.create') }}" class="px-4 py-2 bg-red-600 text-white rounded">➕ Nova Turma</a>
        </div>

        <table class="min-w-full bg-white border rounded shadow">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Nome</th>
                    <th class="px-4 py-2 text-left">Clube</th>
                     <th class="px-4 py-2 text-left">Instrutores</th>
                    <th class="px-4 py-2 text-left">Horário</th>
                    <th class="px-4 py-2 text-left">Alunos</th>
                    <th class="px-4 py-2 text-left">Dias da Semana</th>
                    <th class="px-4 py-2 text-center">Ações</th>
                </tr>
            </thead>
             <tbody class="divide-y divide-gray-200">
                @foreach($classes as $class)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $class->name }}</td>
                        <td class="px-4 py-2">{{ $class->club->name ?? '—' }}</td>
                        <td class="px-4 py-2">{!! implode("<br>",$class->instructors->pluck("name")->toArray()) ?? '—' !!}</td>
                        
                        <td class="px-4 py-2">{{ $class->start_time }} - {{ $class->end_time }}</td>
                        <td class="px-4 py-2">
                            {{ $class->students->count() }}
                        </td>
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

                            // garante que $weekDaysRaw seja sempre array
                            $weekDaysRaw = is_array($class->week_days) 
                                ? $class->week_days 
                                : json_decode($class->week_days, true) ?? [];

                            $weekDays = [];
                            foreach ($weekDaysRaw as $day) {
                                $weekDays[] = $daysMap[$day] ?? $day;
                            }
                        @endphp

                        <td class="px-4 py-2">
                            {!! !empty($weekDays) ? implode('<br> ', $weekDays) : '—' !!}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('classes.pdf', $class->id) }}" class="text-black-600 mr-2">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('classes.attendance', $class->id) }}" class="text-green-600 mr-2">
                                <i class="fa fa-calendar-check" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('classes.edit', $class) }}" class="text-blue-600 mr-2">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Excluir turma?')" class="text-red-600">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $classes->links() }}</div>
    </div>
</x-app-layout>
