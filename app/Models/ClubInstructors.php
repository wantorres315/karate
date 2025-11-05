<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubInstructors extends Model
{
    protected $fillable = ['club_id', 'profile_id'];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
