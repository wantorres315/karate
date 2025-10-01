<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = [
        'name',
        'acronym',
        'logo',
        'username_fnkp',
        'username_password_fnkp',
        'certificate_fnkp',
        'status_year',
        'status',
        'address',
        'postal_code',
        'city',
        'district',
        'cell_number',
        'phone_number',
        'email',
        'website',
        'responsible_name',
        'responsible_cell_number',
        'responsible_telephone_number',
        'responsible_position',
    ];

    public function instructors()
    {
        return $this->belongsToMany(
            User::class,
            'club_instructors', // tabela pivot
            'club_id',          // FK em club_instructors para Club
            'user_id'           // FK em club_instructors para User
        );
    }
}
