<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSubject extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'jp',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
