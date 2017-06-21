<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Aircraft
 *
 * @package App\Models
 * @version June 9, 2017, 1:06 am UTC
 */
class Aircraft extends Model
{
    public $table = 'aircraft';

    protected $dates = ['deleted_at'];

    public $fillable
        = [
            'aircraft_class_id',
            'icao',
            'name',
            'registration',
            'tail_number',
            'active',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'icao'         => 'string',
            'name'         => 'string',
            'registration' => 'string',
            'active'       => 'boolean',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'icao'         => 'required|max:5',
            'name'         => 'required',
            'active'       => '',
        ];

    /**
     * foreign key
     */
    public function class()
    {
        return $this->belongsTo(
            'App\Models\AircraftClass',
            'aircraft_class_id'
        );
    }

    public function fares()
    {
        $r = $this->belongsToMany(
            'App\Models\Fare',
            'aircraft_fare'
        )->withPivot('price', 'cost', 'capacity');
        return $r;
    }
}
