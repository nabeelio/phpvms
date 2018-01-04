<?php

namespace App\Models;

/**
 * Class PirepField
 *
 * @package App\Models
 */
class PirepField extends BaseModel
{
    public $table = 'pirep_fields';
    public $timestamps = false;

    public $fillable = [
        'name',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required',
    ];
}
