<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\TypeEvent;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'type', 'start', 'end', 'description', 'location', 'organization', 'color'
    ];
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function getTypeLabelAttribute()
    {
            // Retorna o nome legível com base no Enum
        return match ($this->type) {
            TypeEvent::T->name => 'Treinos Técnicos',
            TypeEvent::TC->name => 'Treinos de Competição (Kata/Kumite)',
            TypeEvent::F->name => 'Formação / Cursos / Reuniões',
            TypeEvent::EN->name => 'Encontros: Treino / Exames / Torneio',
            TypeEvent::C->name => 'Competições / Torneios / Provas',
            TypeEvent::E->name => 'Estágios Técnicos / Competição',
            TypeEvent::EX->name => 'Exames de Graduação',
            default => 'Outro',
        };
    }
    public static function booted()
    {
        static::creating(function ($event) {
            $colors = [
                'T' => '#ece003ff', // Treinos
                "TC" => '#650350ff', //Treinos de competicao kata e kumite
                'F' => '#2563eb', // Formação
                'EN' => '#e98d05ff', // Encontros
                'C' => '#dc2626', // Competições
                'E' => '#34f604ff', // Estágios
                "EX" => '#135303ff',
            ];

            $event->color = $colors[$event->type] ?? '#6b7280';
        });
    }
    
}
