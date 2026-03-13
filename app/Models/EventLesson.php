<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLesson extends Model
{
    protected $fillable = [
        'event_id', 'title', 'description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
