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

    /**
     * Get all of the subject's monitoring items.
     */
    public function monitoringItems()
    {
        return $this->morphMany(MonitoringItem::class, 'monitorable');
    }
}
