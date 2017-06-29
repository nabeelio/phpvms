<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class PirepField
 *
 * @package App\Models
 */
class PirepField extends Model
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
