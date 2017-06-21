<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AircraftClass
 * @package App\Models
 * @version June 9, 2017, 8:10 pm UTC
 */
class AircraftClass extends Model
{
    public $table = 'aircraft_classes';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'code',
        'name',
        'notes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string',
        'notes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required',
        'name' => 'required'
    ];
}
