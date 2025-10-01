<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubInstructors extends Model
{
    protected $fillable = ['club_id', 'user_id'];
}
