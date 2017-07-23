<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Airline
 * @package App\Models
 */
class Airline extends Model
{
    public $table = 'airlines';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'code',
        'iata',
        'name',
        'fuel_100ll_cost',
        'fuel_jeta_cost',
        'fuel_mogas_cost',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string',
        'fuel_100ll_cost' => 'double',
        'fuel_jeta_cost' => 'double',
        'fuel_mogas_cost' => 'double',
        'active' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required|max:3|unique:airlines',
        'name' => 'required',
    ];

}
