<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Fare
 *
 * @package App\Models
 */
class Fare extends Model
{
    public $table = 'fares';

    protected $dates = ['deleted_at'];

    public $fillable
        = [
            'code',
            'name',
            'price',
            'cost',
            'notes',
            'active',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'code'   => 'string',
            'name'   => 'string',
            'price'  => 'float',
            'cost'   => 'float',
            'notes'  => 'string',
            'active' => 'boolean',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'code' => 'required',
            'name' => 'required',
        ];

    /**
     * any foreign keys
     */

    public function aircraft() {
        return $this->belongsToMany(
            'App\Models\Aircraft',
            'aircraft_fare'
        )->withPivot('price', 'cost', 'capacity');
    }

}
