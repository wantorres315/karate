<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\TypeEvent;

class AgendaController extends Controller
{
    // Listar todos os eventos
    public function index()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'description' => $event->description,
                'type' => $event->getTypeLabelAttribute(), // nome legível (do enum)
                'color' => $event->color ?? $this->getColorByType($event->type),
            ];
        });

        return response()->json($events);
    }

    // Criar evento
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'location' => 'nullable|string',
            'organization' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        // Define a cor com base no tipo, se não informada
        $validated['color'] = $validated['color'] ?? $this->getColorByType($validated['type'] ?? null);

        $event = Event::create([
            'title' => $validated['title'],
            'start' => Carbon::parse($validated['start']),
            'end' => Carbon::parse($validated['end']),
            'description' => $validated['description'] ?? 'Sem descrição',
            'type' => $validated['type'] ?? null,
            'location' => $validated['location'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'color' => $validated['color'],
        ]);

        return response()->json($event, 201);
    }

    // Atualizar evento
    public function update(Request $request, Event $event)
    {
        $event->update([
            'start' => $request->start,
            'end' => $request->end ?? $request->start,
        ]);

        return response()->json($event);
    }

    private function getColorByType(?string $type): string
    {
        return match ($type) {
            TypeEvent::T->value => '#2563eb', // Azul — Treino Técnico
            TypeEvent::TC->value => '#16a34a', // Verde — Treino de Competição
            TypeEvent::F->value => '#facc15', // Amarelo — Formação
            TypeEvent::EN->value => '#fb923c', // Laranja — Encontros
            TypeEvent::C->value => '#dc2626', // Vermelho — Competições
            TypeEvent::E->value => '#9333ea', // Roxo — Estágios
            TypeEvent::EX->value => '#0ea5e9', // Azul Claro — Exames
            default => '#1E90FF', // Azul padrão (fallback)
        };
    }

    // Deletar evento
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Evento deletado']);
    }

    public function exportPdf(Request $request)
    {
        $startDate = Carbon::parse($request['start_date'])->startOfDay();
        $endDate = isset($request['end_date'])
            ? Carbon::parse($request['end_date'])->endOfDay()
            : $startDate->copy()->endOfDay();

        // Buscar eventos no intervalo
        $events = Event::whereBetween('start', [$startDate, $endDate])
            ->orderBy('start', 'asc')
            ->get();

        // Agrupar por mês/ano
        $groupedEvents = $events->groupBy(function ($event) {
            return Carbon::parse($event->start)->format('F Y'); // Ex: "September 2025"
        });
         $name= "Calendário KAK ". date('Y', strtotime($startDate)) . '-' . date('Y', strtotime($endDate));
        // Enviar para a view
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('schedule.pdf', [
            'groupedEvents' => $groupedEvents,
            'startDate' => $startDate,
            'endDate' => $endDate,
            "name" => $name,
        ]);
                 
        return $pdf->download(
            $name. '.pdf'
        );
    }

}
