<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Flight
 *
 * @package App\Models
 */
class Flight extends Model
{
    use SoftDeletes;

    public $table = 'flights';

    protected $dates = ['deleted_at'];

    public $fillable
        = [
            'airline_id',
            'flight_number',
            'route_code',
            'route_leg',
            'dpt_airport_id',
            'arr_airport_id',
            'alt_airport_id',
            'route',
            'dpt_time',
            'arr_time',
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
            'flight_number' => 'string',
            'route_code'    => 'string',
            'route_leg'     => 'string',
            'route'         => 'string',
            'dpt_time'      => 'string',
            'arr_time'      => 'string',
            'notes'         => 'string',
            'active'        => 'boolean',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'flight_number'  => 'required',
            'dpt_airport_id' => 'required',
            'arr_airport_id' => 'required',
        ];

    public function dpt_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'dpt_airport_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'arr_airport_id');
    }

    public function alt_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'alt_airport_id');
    }

    public function aircraft()
    {
        return $this->belongsToMany('App\Models\Aircraft', 'flight_aircraft');
    }
}
