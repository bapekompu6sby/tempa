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
        'checked',
    ];

    /**
     * Type casts for attributes.
     *
     * @var array
     */
    protected $casts = [
        'checked' => 'boolean',
    ];

    /**
     * The event this document belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Automatically set checked attribute based on link, file_path, or attachments.
     */
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $hasLink = !empty($model->link);
            $hasFilePath = !empty($model->file_path);
            $hasAttachment = $model->files()->count() > 0;
            $model->checked = $hasLink || $hasFilePath || $hasAttachment;
        });
    }

    /**
     * Multiple files (attachments) for this document.
     */
    public function files()
    {
        return $this->hasMany(\App\Models\EventDocumentFile::class);
    }
}
