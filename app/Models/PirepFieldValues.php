<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class PirepField
 *
 * @package App\Models
 */
class PirepFieldValues extends Model
{
    public $table = 'pirep_field_values';

    public $fillable
        = [
            'pirep_id',
            'name',
            'value',
            'source',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'name'   => 'string',
            'value'  => 'string',
            'source' => 'integer',
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
