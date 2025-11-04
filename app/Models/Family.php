<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'name',
        'user_id',
    ];

    // Usuário familiar (representante da família)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Membros da família (profiles conectados)
    public function members()
    {
        return $this->belongsToMany(Profile::class, 'family_members');
    }
}