<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'is_active',
        'ownership',
        'order',
    ];

    /**
     * Get the items that were generated from this template.
     */
    public function items()
    {
        return $this->hasMany(MonitoringItem::class, 'monitoring_template_id');
    }
}
