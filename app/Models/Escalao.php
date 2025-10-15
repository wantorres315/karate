<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escalao extends Model
{

    protected $table = "escaloes";
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];
}
