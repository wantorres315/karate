<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'family_id',
        'profile_id',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}