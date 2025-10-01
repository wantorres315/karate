{{-- resources/views/components/dashboard/progress-card.blade.php --}}
@props(['student'])

<div class="border rounded p-4">
    <h3 class="font-bold">{{ $student->name }}</h3>
    <p>Peso atual: {{ $student->latest_weight }} kg</p>
    <p>Último treino: {{ optional($student->last_training_date)->format('d/m/Y') ?? 'Sem treino' }}</p>
</div>
