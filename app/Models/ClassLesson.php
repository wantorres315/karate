<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassLesson extends Model
{
    protected $fillable = ['classe_id', 'lesson_date'];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
