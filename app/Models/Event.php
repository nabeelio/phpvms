<?php

namespace App\Models;

use App\Contracts\Model;

class Event extends Model
{
    public $table = 'events';

    protected $fillable = [
        'type',
        'name',
        'description',
        'start_date',
        'end_date',
        'active',
    ];

    // Validation
    public static $rules = [
        'type'        => 'required|numeric',
        'name'        => 'required|max:250',
        'description' => 'nullable',
        'start_date'  => 'required',
        'end_date'    => 'required',
        'active'      => 'nullable',
    ];

    // Carbon Coverted Dates
    public $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    // Attributes may be defined later if necessary

    // Relationships may be defined later if necessary
}
