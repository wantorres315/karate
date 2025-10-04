<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        "name",
        'user_id',
        'number_kak',
        'number_fnkp',
        'number_cit',
        'number_tptd',
        'number_jks',
        'arbitrator_id',
        'admission_date',
        'photo',
        'father_name',
        'mother_name',
        'document_type',
        'document_number',
        'birth_date',
        'nationality',
        'profession',
        'address',
        'postal_code',
        'city',
        'district',
        'cell_number',
        'phone_number',
        'email',
        'contact',
        'contact_number',
        'contact_cell',
        'contact_email',
        'observations',
        'club_id',
        'status',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'id');
    }

    public function user(){
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function lastGraduation()
    {
        return $this->hasOne(GraduationUser::class, 'profile_id', 'id')
            ->latestOfMany('date'); // usa a data para pegar a última
    }
    public function graduations()
    {
        return $this->hasMany(GraduationUser::class, 'profile_id', 'id');
    }
    
}
