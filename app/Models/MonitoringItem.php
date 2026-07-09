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
        'monitoring_template_id',
        'order',
        'value',
    ];

    /**
     * Get the parent monitorable model (Event, AsnEvent, or AsnEventSubject).
     */
    public function monitorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the associated monitoring template.
     */
    public function template()
    {
        return $this->belongsTo(MonitoringTemplate::class, 'monitoring_template_id');
    }
}
