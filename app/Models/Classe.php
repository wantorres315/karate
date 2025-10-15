<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Classe extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'club_id',
        "week_days",
        "startDate",
        "endDate",
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    protected $casts = [
        'week_days' => 'array',
    ];

    public function students()
    {
        return $this->belongsToMany(
            Profile::class,      // Modelo relacionado
            'class_profile',     // Tabela pivot
            'classe_id',         // Chave estrangeira da turma na pivot
            'profile_id'         // Chave estrangeira do aluno na pivot
        )->withTimestamps();
    }

    public function instructors()
    {
        return $this->belongsToMany(Profile::class, 'classe_instructor', 'classe_id', 'profile_id');
    }
    public function lessons()
    {
        return $this->hasMany(ClassLesson::class, 'classe_id');
    }
     public function generateLessons()
    {
        if (empty($this->week_days) || !$this->startDate || !$this->endDate) {
            return; // nada a fazer se faltar info
        }

        $daysMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];

        // converte os dias escolhidos em números
        $weekDaysArray = is_array($this->week_days) 
            ? $this->week_days 
            : json_decode($this->week_days, true);

        $days = collect($weekDaysArray)->map(fn($d) => $daysMap[$d])->toArray();


        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        $lessons = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (in_array($date->dayOfWeek, $days)) {
                $lessons[] = [
                    'classe_id' => $this->id,
                    'lesson_date' => $date->toDateString(),
                ];
            }
        }

        // limpa aulas antigas para evitar duplicados
        $this->lessons()->delete();

        // insere novas aulas
        if (!empty($lessons)) {
            ClassLesson::insert($lessons);
        }
    }
}
