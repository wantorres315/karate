<x-app-layout>
<div class="p-6 max-w-6xl w-full mx-auto bg-white rounded-md shadow-md">
    <h2 class="text-2xl font-semibold mb-6">📋 Presenças — {{ $classe->name }}</h2>

    <form method="POST" action="{{ route('classes.saveAttendance', $classe) }}">
        @csrf

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm border-collapse border">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="border px-3 py-2 bg-gray-100 sticky left-0 z-20" style="min-width: 120px;">Aluno</th>
                        @foreach($lessons as $lesson)
                            @php
                                $date = \Carbon\Carbon::parse($lesson->lesson_date);
                                $isToday = $date->isToday();
                            @endphp
                            <th class="border px-3 py-2 text-center {{ $isToday ? 'bg-blue-200 font-semibold' : '' }}">
                                {{ $date->format('d/m') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2 font-medium bg-gray-50 sticky left-0 z-10" style="min-width: 120px;">
                                {{ $student->name }}
                            </td>
                            @foreach($lessons as $lesson)
                                @php
                                    $att = $lesson->attendances->firstWhere('student_id', $student->id);
                                    $date = \Carbon\Carbon::parse($lesson->lesson_date);
                                    $isToday = $date->isToday();
                                @endphp
                                <td class="border px-3 py-2 text-center {{ $isToday ? 'bg-blue-100' : '' }}">
                                    <input type="checkbox"
                                           name="attendance[{{ $lesson->id }}][{{ $student->id }}]"
                                           value="1"
                                           {{ $att && $att->present ? 'checked' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-right">
            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold transition-colors">
                💾 Salvar
            </button>
        </div>
    </form>
</div>

{{-- Script para centralizar o dia atual --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const todayCol = document.querySelector('th.bg-blue-200');
    if(todayCol) {
        const tableWrapper = todayCol.closest('.overflow-x-auto');
        const offset = todayCol.offsetLeft - (tableWrapper.clientWidth / 2) + (todayCol.clientWidth / 2);
        tableWrapper.scrollLeft = offset;
    }
});
</script>
</x-app-layout>
