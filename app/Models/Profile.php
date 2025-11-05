<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        "is_treinador",
        "nif",
        "belt"
    ];

      protected $appends = ['escalao']; 
      
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'id');
    }

    // Dojos onde o treinador pode dar treino (via pivot existente)
    public function trainingClubs()
    {
        return $this->belongsToMany(Club::class, 'club_instructors', 'profile_id', 'club_id');
    }

    // Helper: checar autorização para um dojo específico
    public function canTrainInClub(int|string $clubId): bool
    {
        if (!$this->is_treinador) {
            return false;
        }

        if ((int)$this->club_id === (int)$clubId) {
            return true;
        }

        return $this->trainingClubs()->wherePivot('club_id', $clubId)->exists();
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


    public function getEscalaoAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        $birthDate = Carbon::parse($this->birth_date);

        $escalao = Escalao::whereDate('start_date', '<=', $birthDate)
            ->whereDate('end_date', '>=', $birthDate)
            ->first();

        return $escalao ? $escalao->name : 'Sem Escalão';
    }

    public function classes()
    {
        // Relação correta: Profile -> many-to-many -> Classe (tabela pivot: class_profiles)
        return $this->belongsToMany(Classe::class, 'class_profiles', 'profile_id', 'classe_id')
            ->withTimestamps();
    }

    // (Opcional) se você tiver um modelo ClassProfile para a pivot e quiser acessar os registros da pivot:
    public function classLinks()
    {
        return $this->hasMany(ClassProfile::class, 'profile_id', 'id');
    }

    public function families()
    {
        return $this->belongsToMany(FamilyMember::class, 'family_members');
    }

    public function familyMember()
    {
        return $this->hasOne(FamilyMember::class, 'profile_id', 'id');
    }

    protected $casts = [
        'is_treinador' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function (Profile $profile) {
            if ($profile->wasChanged('is_treinador') && $profile->is_treinador === false) {
                // remove todos os clubes associados como instrutor
                $profile->trainingClubs()->detach();
            }
        });
    }
}
