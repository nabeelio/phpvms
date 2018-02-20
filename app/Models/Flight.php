<?php

namespace App\Models;

use App\Support\Units\Distance;
use App\Support\Units\Time;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

use App\Models\Traits\HashId;

class Flight extends BaseModel
{
    use HashId;

    public const ID_MAX_LENGTH = 12;

    public $table = 'flights';
    public $incrementing = false;

    public $fillable = [
        'id',
        'airline_id',
        'flight_number',
        'route_code',
        'route_leg',
        'dpt_airport_id',
        'arr_airport_id',
        'alt_airport_id',
        'dpt_time',
        'arr_time',
        'level',
        'distance',
        'flight_time',
        'flight_type',
        'route',
        'notes',
        'has_bid',
        'active',
    ];

    protected $casts = [
        'flight_number' => 'integer',
        'level'         => 'integer',
        'distance'      => 'float',
        'flight_time'   => 'integer',
        'flight_type'   => 'integer',
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

        if (filled($this->route_code)) {
            $flight_id .= '/C' . $this->route_code;
        }

        if (filled($this->route_leg)) {
            $flight_id .= '/L' . $this->route_leg;
        }

        return $flight_id;
    }

    /**
     * Return a new Length unit so conversions can be made
     * @return int|Distance
     */
    public function getDistanceAttribute()
    {
        if (!array_key_exists('distance', $this->attributes)) {
            return null;
        }

        try {
            $distance = (float) $this->attributes['distance'];
            return new Distance($distance, config('phpvms.internal_units.distance'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }

    /**
     * Set the distance unit, convert to our internal default unit
     * @param $value
     */
    public function setDistanceAttribute($value)
    {
        if($value instanceof Distance) {
            $this->attributes['distance'] = $value->toUnit(
                config('phpvms.internal_units.distance')
            );
        } else {
            $this->attributes['distance'] = $value;
        }
    }

    /**
     * @return Time
     */
    /*public function getFlightTimeAttribute()
    {
        if (!array_key_exists('flight_time', $this->attributes)) {
            return null;
        }

        return new Time($this->attributes['flight_time']);
    }*/

    /**
     * @param $value
     */
    /*public function setFlightTimeAttribute($value)
    {
        if ($value instanceof Time) {
            $this->attributes['flight_time'] = $value->getMinutes();
        } else {
            $this->attributes['flight_time'] = $value;
        }
    }*/

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
