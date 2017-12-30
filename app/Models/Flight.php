<?php

namespace App\Models;

use App\Models\Traits\HashId;

class Flight extends BaseModel
{
    use HashId;

    public $table = 'flights';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
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
        'has_bid',
        'active',
    ];

    protected $casts = [
        'flight_number' => 'integer',
        'route_code'    => 'string',
        'route_leg'     => 'string',
        'route'         => 'string',
        'dpt_time'      => 'string',
        'arr_time'      => 'string',
        'notes'         => 'string',
        'has_bid'       => 'boolean',
        'active'        => 'boolean',
    ];

    public static $rules = [
        'flight_number'  => 'required',
        'dpt_airport_id' => 'required',
        'arr_airport_id' => 'required',
    ];

    /**
     * Get the flight ident, e.,g JBU1900
     */
    public function getIdentAttribute()
    {
        $flight_id = $this->airline->code;
        $flight_id .= $this->flight_number;

        # TODO: Add in code/leg if set

        return $flight_id;
    }

    /**
     * Relationship
     */

    public function airline()
    {
        return $this->belongsTo('App\Models\Airline', 'airline_id');
    }

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

    public function fields()
    {
        return $this->hasMany('App\Models\FlightFields', 'flight_id');
    }

    public function subfleets()
    {
        return $this->belongsToMany('App\Models\Subfleet', 'subfleet_flight');
    }
}
