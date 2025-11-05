<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class EventInstruction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'instruction_id',
        'checked',
        'linkable',
        'link',
        'link_label',
        'phase',
        'full_elearning',
        'distance_learning',
        'blended_learning',
        'classical',
    ];

    /**
     * Instruction relation
     */
    public function instruction()
    {
        return $this->belongsTo(Instruction::class);
    }

    /**
     * Event relation
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Boot model events to keep parent Event status in sync when instructions change.
     */
    protected static function booted()
    {
        static::saved(function (self $model) {
            // when 'checked' toggles, update parent event status
            if (method_exists($model, 'wasChanged') && $model->wasChanged('checked')) {
                try {
                    $ev = $model->event;
                    if ($ev) {
                        $ev->check_status();
                    }
                } catch (\Exception $e) {
                    // swallow to avoid breaking the save flow
                }
            }
        });

        static::deleted(function (self $model) {
            // deletion may affect counts; ensure parent event status updates
            try {
                $ev = $model->event;
                if ($ev) {
                    $ev->check_status();
                }
            } catch (\Exception $e) {
                // ignore
            }
        });
    }
}
