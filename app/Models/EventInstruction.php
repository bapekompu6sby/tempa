<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
