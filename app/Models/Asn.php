<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asn extends Model
{
    protected $fillable = [
        'nip',
        'name',
        'job_title',
        'phone_number',
        'email',
        'birth_city',
        'birth_date',
        'gender',
        'rank_grade',
        'latest_education',
        'office_address',
        'asn_type',
        'asn_source',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class)
                    ->withPivot(['passing_status', 'participant_type', 'participant_status'])
                    ->withTimestamps();
    }
}
