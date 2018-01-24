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
        'level',
        'distance',
        'notes',
        'has_bid',
        'active',
    ];

    protected $casts = [
        'flight_number' => 'integer',
        'level'         => 'integer',
        'distance'      => 'float',
        'has_bid'       => 'boolean',
        'active'        => 'boolean',
    ];

    public static $rules = [
        'airline_id'     => 'required|exists:airlines,id',
        'flight_number'  => 'required',
        'route_code'     => 'nullable',
        'route_leg'      => 'nullable',
        'dpt_airport_id' => 'required',
        'arr_airport_id' => 'required',
        'level'          => 'nullable',
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
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo(Airport::class, 'dpt_airport_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo(Airport::class, 'arr_airport_id');
    }

    public function alt_airport()
    {
        return $this->belongsTo(Airport::class, 'alt_airport_id');
    }

    public function fares()
    {
        return $this->belongsToMany(Fare::class, 'flight_fare')
                    ->withPivot('price', 'cost', 'capacity');
    }

    public function fields()
    {
        return $this->hasMany(FlightFields::class, 'flight_id');
    }

    public function subfleets()
    {
        return $this->belongsToMany(Subfleet::class, 'subfleet_flight');
    }
}
