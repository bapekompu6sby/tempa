<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDocumentFile extends Model
{
    protected $fillable = [
        'event_document_id',
        'original_name',
        'file_path',
        'mime',
        'size',
    ];

    public function eventDocument()
    {
        return $this->belongsTo(EventDocument::class);
    }
}
