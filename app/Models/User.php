<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function lastGraduation()
    {
        return $this->hasOne(GraduationUser::class, 'user_id', 'id')
            ->latestOfMany('date'); // usa a data para pegar a última
    }

    public function graduations()
    {
        return $this->hasMany(GraduationUser::class, 'user_id', 'id');
    }

    public function clubsAsInstructor()
    {
        return $this->belongsToMany(
            Club::class,
            'club_instructors', // tabela pivot
            'user_id',          // FK em club_instructors para User
            'club_id'           // FK em club_instructors para Club
        );
    }

}
