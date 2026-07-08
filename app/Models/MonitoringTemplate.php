<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'ownership',
        'order',
    ];
}
