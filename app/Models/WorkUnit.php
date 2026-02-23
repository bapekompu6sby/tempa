<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkUnit extends Model
{
    protected $fillable = [
        'name',
        'unit_organization',
        'address',
    ];
}
