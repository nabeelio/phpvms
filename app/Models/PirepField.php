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

    public $fillable
        = [
            'name',
            'required',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'name'     => 'string',
            'required' => 'integer',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'name' => 'required',
        ];
}
