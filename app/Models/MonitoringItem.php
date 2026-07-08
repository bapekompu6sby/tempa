<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MonitoringItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'monitorable_id',
        'monitorable_type',
        'order',
    ];

    /**
     * Get the parent monitorable model (Event or EventSubject).
     */
    public function monitorable(): MorphTo
    {
        return $this->morphTo();
    }
}
