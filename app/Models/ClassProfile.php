<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'profile_id',
    ];

    

    public function classes()
    {
        return $this->belongsToMany(
            Classe::class,
            'class_profile',
            'profile_id',
            'classe_id'
        )->withTimestamps();
    }
}
