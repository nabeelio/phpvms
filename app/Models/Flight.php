<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\Days;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string     id
 * @property mixed      ident
 * @property Airline    airline
 * @property int        airline_id
 * @property mixed      flight_number
 * @property mixed      callsign
 * @property mixed      route_code
 * @property int        route_leg
 * @property bool       has_bid
 * @property Collection field_values
 * @property Collection fares
 * @property Collection subfleets
 * @property int        days
 * @property int        distance
 * @property int        flight_time
 * @property string     route
 * @property string     dpt_time
 * @property string     arr_time
 * @property string     flight_type
 * @property string     notes
 * @property int        level
 * @property float      load_factor
 * @property float      load_factor_variance
 * @property float      pilot_pay
 * @property Airport    dpt_airport
 * @property Airport    arr_airport
 * @property Airport    alt_airport
 * @property string     dpt_airport_id
 * @property string     arr_airport_id
 * @property string     alt_airport_id
 * @property int        active
 * @property Carbon     start_date
 * @property Carbon     end_date
 */
class Flight extends Model
{
    use HashIdTrait;

    public $table = 'flights';

    /** The form wants this */
    public $hours;
    public $minutes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'airline_id',
        'flight_number',
        'callsign',
        'route_code',
        'route_leg',
        'dpt_airport_id',
        'arr_airport_id',
        'alt_airport_id',
        'dpt_time',
        'arr_time',
        'days',
        'level',
        'distance',
        'flight_time',
        'flight_type',
        'load_factor',
        'load_factor_variance',
        'pilot_pay',
        'route',
        'notes',
        'start_date',
        'end_date',
        'has_bid',
        'active',
        'visible',
    ];

    protected $casts = [
        'flight_number'        => 'integer',
        'days'                 => 'integer',
        'level'                => 'integer',
        'distance'             => 'float',
        'flight_time'          => 'integer',
        'start_date'           => 'date',
        'end_date'             => 'date',
        'load_factor'          => 'double',
        'load_factor_variance' => 'double',
        'pilot_pay'            => 'float',
        'has_bid'              => 'boolean',
        'route_leg'            => 'integer',
        'active'               => 'boolean',
        'visible'              => 'boolean',
    ];

    public static $rules = [
        'airline_id'           => 'required|exists:airlines,id',
        'flight_number'        => 'required',
        'callsign'             => 'string|max:4|nullable',
        'route_code'           => 'nullable',
        'route_leg'            => 'nullable',
        'dpt_airport_id'       => 'required|exists:airports,id',
        'arr_airport_id'       => 'required|exists:airports,id',
        'load_factor'          => 'nullable|numeric',
        'load_factor_variance' => 'nullable|numeric',
        'level'                => 'nullable',
    ];

    /**
     * Return all of the flights on any given day(s) of the week
     * Search using bitmasks
     *
     * @param Days[] $days List of the enumerated values
     *
     * @return Flight
     */
    public static function findByDays(array $days)
    {
        /** @noinspection DynamicInvocationViaScopeResolutionInspection */
        $flights = self::where('active', true);
        foreach ($days as $day) {
            $flights = $flights->where('days', '&', $day);
        }

        return $flights;
    }

    /**
     * Get the flight ident, e.,g JBU1900
     */
    public function getIdentAttribute(): string
    {
        $flight_id = $this->airline->code;
        $flight_id .= $this->flight_number;

        if (filled($this->route_code)) {
            $flight_id .= '-'.$this->route_code;
        }

        if (filled($this->route_leg)) {
            $flight_id .= '-'.$this->route_leg;
        }

        return $flight_id;
    }

    /**
     * Set the distance unit, convert to our internal default unit
     *
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
     * @param $day
     *
     * @return bool
     */
    public function on_day($day): bool
    {
        return ($this->days & $day) === $day;
    }

    /**
     * Return a custom field value
     *
     * @param $field_name
     *
     * @return string
     */
    public function field($field_name): string
    {
        $field = $this->field_values->where('name', $field_name)->first();
        if ($field) {
            return $field['value'];
        }

        return '';
    }

    /**
     * Set the days parameter. If an array is passed, it's
     * AND'd together to create the mask value
     *
     * @param array|int $val
     */
    public function setDaysAttribute($val): void
    {
        if (\is_array($val)) {
            $val = Days::getDaysMask($val);
        }

        $this->attributes['days'] = $val;
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

    public function simbrief()
    {
        // id = key from table, flight_id = reference key
        return $this->belongsTo(SimBrief::class, 'id', 'flight_id');
    }

    public function subfleets()
    {
        return $this->belongsToMany(Subfleet::class, 'flight_subfleet');
    }
}
