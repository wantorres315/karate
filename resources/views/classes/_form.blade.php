@php
    $isEdit = isset($classe);

    // Alunos selecionados
    $selectedStudents = $selectedStudents ?? [];
    if (is_string($selectedStudents)) {
        $selectedStudents = json_decode($selectedStudents, true) ?? [];
    }

    // Instrutores selecionados
    $selectedInstructors = $selectedInstructors ?? [];
    if (is_string($selectedInstructors)) {
        $selectedInstructors = json_decode($selectedInstructors, true) ?? [];
    }

    // Dias da semana
    $selectedWeekDays = old('week_days', $classe->week_days ?? []);
    if (is_string($selectedWeekDays)) {
        $selectedWeekDays = json_decode($selectedWeekDays, true) ?? [];
    }

    $weekDays = [
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
@endphp

<div class="space-y-6">
    {{-- Nome da Turma --}}
    <div>
        <label class="block text-sm font-medium">Nome da Turma</label>
        <input type="text" name="name"
               value="{{ old('name', $classe->name ?? '') }}"
               class="w-full border rounded-md p-2"
               required>
    </div>

    {{-- Descrição --}}
    <div>
        <label class="block text-sm font-medium">Descrição</label>
        <textarea name="description"
                  class="w-full border rounded-md p-2"
                  rows="3">{{ old('description', $classe->description ?? '') }}</textarea>
    </div>

    {{-- Horários --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Hora de Início</label>
            <input type="time" name="start_time"
                   value="{{ old('start_time', $classe->start_time ?? '') }}"
                   class="w-full border rounded-md p-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium">Hora de Fim</label>
            <input type="time" name="end_time"
                   value="{{ old('end_time', $classe->end_time ?? '') }}"
                   class="w-full border rounded-md p-2"
                   required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Data de Início</label>
            <input type="date" name="start_date"
                   value="{{ old('start_date', $classe->start_date ?? ($classe->startDate ?? '')) }}"
                   class="w-full border rounded-md p-2"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium">Data de Fim</label>
            <input type="date" name="end_date"
                   value="{{ old('end_date', $classe->end_date ?? ($classe->endDate ?? '')) }}"
                   class="w-full border rounded-md p-2"
                   required>
        </div>
    </div>

    {{-- Clube --}}
    <div>
        <label class="block text-sm font-medium">Clube</label>
        <select name="club_id" id="clubSelect"
                class="w-full border rounded-md p-2 bg-white"
                required>
            <option value="">Selecione</option>
            @foreach ($clubs as $club)
                <option value="{{ $club->id }}"
                    {{ old('club_id', $classe->club_id ?? '') == $club->id ? 'selected' : '' }}>
                    {{ $club->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Dias da Semana --}}
    <div>
        <label class="block text-sm font-medium">Dias da Semana</label>
        <div class="flex flex-wrap gap-3 mt-2">
            @php
                $savedWeekDays = is_array($selectedWeekDays) ? $selectedWeekDays : [];
            @endphp

            @foreach([
                'sunday' => 'Domingo',
                'monday' => 'Segunda',
                'tuesday' => 'Terça',
                'wednesday' => 'Quarta',
                'thursday' => 'Quinta',
                'friday' => 'Sexta',
                'saturday' => 'Sábado'
            ] as $key => $label)
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="week_days[]"
                           value="{{ $key }}"
                           class="rounded"
                           {{ in_array($key, $savedWeekDays ?? []) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Alunos --}}
    <div>
        <label class="block text-sm font-medium">Alunos</label>
        <div id="studentsContainer" class="border rounded-md p-3 h-60 overflow-y-auto bg-gray-50">
            @if(isset($students))
                @forelse ($students as $student)
                    @php
                        $isChecked = in_array($student->id, $selectedStudents) ? 'checked' : '';
                    @endphp
                    <div class="mb-1">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="students[]" value="{{ $student->id }}" {{ $isChecked }}>
                            <span>{{ $student->number_kak }} - {{ $student->name ?? '' }} - {{ $student->escalao ?? '' }}</span>
                        </label>
                        <hr class="border-t border-gray-300 mt-1" style="border:1px solid #cecece">
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Nenhum aluno disponível.</p>
                @endforelse
            @else
                <p class="text-gray-500 text-sm">Selecione um clube para carregar os alunos.</p>
            @endif
        </div>
    </div>

    {{-- Instrutores --}}
    <div>
        <label class="block text-sm font-medium">Instrutores</label>
        <div id="instructorsContainer" class="border rounded-md p-3 h-40 overflow-y-auto bg-gray-50">
            @if(isset($instructors) && $instructors->count() > 0)
                @foreach($instructors as $instrutor)
                    @php
                        $isChecked = in_array($instrutor->id, $selectedInstructors) ? 'checked' : '';
                    @endphp
                    <div class="mb-1">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="instructors[]" value="{{ $instrutor->id }}" {{ $isChecked }}>
                            <span>{{ $instrutor->name }}</span>
                        </label>
                        <hr class="border-t border-gray-300 mt-1" style="border:1px solid #cecece">
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 text-sm">Nenhum instrutor disponível.</p>
            @endif
        </div>
    </div>

    {{-- Botão --}}
    <div class="flex justify-end">
        <x-primary-button>
            {{ $isEdit ? 'Atualizar Turma' : 'Criar Turma' }}
        </x-primary-button>
    </div>
</div>

{{-- Script para carregar alunos dinamicamente --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const clubSelect = document.getElementById('clubSelect');
    const studentsContainer = document.getElementById('studentsContainer');
    const instructorsContainer = document.getElementById('instructorsContainer');

    const selectedStudents = @json($selectedStudents ?? []);
    const selectedInstructors = @json($selectedInstructors ?? []);

    clubSelect.addEventListener('change', async (e) => {
        const clubId = e.target.value;

        studentsContainer.innerHTML = '<p class="text-gray-500 text-sm">🔄 Carregando alunos...</p>';
        instructorsContainer.innerHTML = '<p class="text-gray-500 text-sm">🔄 Carregando instrutores...</p>';

        if (!clubId) {
            studentsContainer.innerHTML = '<p class="text-gray-500 text-sm">Selecione um clube primeiro.</p>';
            instructorsContainer.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/clubs/${clubId}/members`);
            if (!response.ok) throw new Error('Falha ao buscar membros');
            const data = await response.json().catch(() => ({}));

            const members = Array.isArray(data?.students) ? data.students : [];
            const instructors = Array.isArray(data?.instructors) ? data.instructors : [];

            // Alunos
            if (members.length === 0) {
                studentsContainer.innerHTML = '<p class="text-gray-500 text-sm">Nenhum aluno encontrado neste clube.</p>';
            } else {
                studentsContainer.innerHTML = members.map((student) => `
                    <div class="mb-1">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="students[]" value="${student.id}" ${selectedStudents.includes(student.id) ? 'checked' : ''}>
                            <span>${student.number_kak ?? ''} - ${student.name ?? ''} - ${student.escalao ?? ''}</span>
                        </label>
                        <hr class="border-t border-gray-300 mt-1" style="border:1px solid #cecece">
                    </div>
                `).join('');
            }

            // Instrutores
            if (instructors.length === 0) {
                instructorsContainer.innerHTML = '<p class="text-gray-500 text-sm">Nenhum instrutor disponível neste clube.</p>';
            } else {
                instructorsContainer.innerHTML = instructors.map((instructor) => `
                    <div class="mb-1">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="instructors[]" value="${instructor.id}" ${selectedInstructors.includes(instructor.id) ? 'checked' : ''}>
                            <span>${instructor.name ?? ''}${instructor.role ? ' - ' + instructor.role : ''}</span>
                        </label>
                        <hr class="border-t border-gray-300 mt-1" style="border:1px solid #cecece">
                    </div>
                `).join('');
            }

        } catch (error) {
            console.error(error);
            studentsContainer.innerHTML = '<p class="text-red-600 text-sm">Erro ao carregar alunos.</p>';
            instructorsContainer.innerHTML = '<p class="text-red-600 text-sm">Erro ao carregar instrutores.</p>';
        }
    });

    if (clubSelect.value) {
        clubSelect.dispatchEvent(new Event('change'));
    }
});
</script>
