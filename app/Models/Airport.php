<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Airport
 * @package App\Models
 */
class Airport extends Model
{
    public $table = 'airports';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'icao',
        'name',
        'location',
        'fuel_100ll_cost',
        'fuel_jeta_cost',
        'fuel_mogas_cost',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'icao' => 'required|unique:airports'
    ];

    public function save(array $options = [])
    {
        if(in_array('icao', $options)) {
            $options['icao'] = strtoupper($options['icao']);
        }

        return parent::save($options);
    }
}
