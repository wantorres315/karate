<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['class_lesson_id', 'student_id', 'present'];

    public function lesson()
    {
        return $this->belongsTo(ClassLesson::class, 'class_lesson_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
