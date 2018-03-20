<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use Illuminate\Support\Collection;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

/**
 * @property string     id
 * @property Airline    airline
 * @property mixed      flight_number
 * @property mixed      route_code
 * @property mixed      route_leg
 * @property Collection field_values
 */
class Flight extends Model
{
    use HashIdTrait;

    public $table = 'flights';
    public $incrementing = false;

    /** The form wants this */
    public $hours, $minutes;

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
    public function getIdentAttribute(): string
    {
        $flight_id = $this->airline->code;
        $flight_id .= $this->flight_number;

        if (filled($this->route_code)) {
            $flight_id .= '/C'.$this->route_code;
        }

        if (filled($this->route_leg)) {
            $flight_id .= '/L'.$this->route_leg;
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
    public function setDistanceAttribute($value): void
    {
        if ($value instanceof Distance) {
            $this->attributes['distance'] = $value->toUnit(
                config('phpvms.internal_units.distance')
            );
        } else {
            $this->attributes['distance'] = $value;
        }
    }

    /**
     * Return a custom field value
     * @param $field_name
     * @return string
     */
    public function field($field_name): string
    {
        $field = $this->field_values->where('name', $field_name)->first();
        if($field) {
            return $field['value'];
        }

        return '';
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

    public function field_values()
    {
        return $this->hasMany(FlightFieldValue::class, 'flight_id');
    }

    public function subfleets()
    {
        return $this->belongsToMany(Subfleet::class, 'subfleet_flight');
    }
}
