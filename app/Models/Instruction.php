<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role',
        'detail',
        'linkable',
        'link_label',
        'phase',
        'full_elearning',
        'distance_learning',
        'blended_learning',
        'classical',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'linkable' => 'boolean',
        'full_elearning' => 'boolean',
        'distance_learning' => 'boolean',
        'blended_learning' => 'boolean',
        'classical' => 'boolean',
    ];
}
