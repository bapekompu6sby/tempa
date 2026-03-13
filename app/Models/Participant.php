<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'nip',
        'name',
        'work_position',
        'phone_number',
        'email',
        'birth_place',
        'birth_date',
        'gender',
        'class_rank',
        'last_education',
    ];
}