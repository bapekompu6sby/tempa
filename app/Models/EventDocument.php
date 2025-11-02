<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class EventDocument extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'name',
        'full_elearning',
        'distance_learning',
        'blended_learning',
        'classical',
        'notes',
        'link',
        'file_path',
    ];

    /**
     * The event this document belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Multiple files (attachments) for this document.
     */
    public function files()
    {
        return $this->hasMany(\App\Models\EventDocumentFile::class);
    }
}
