<?php

namespace App\Models;

use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Traits\HashId;
use App\Support\Units\Distance;

use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

/**
 * Class Pirep
 *
 * @package App\Models
 */
class Pirep extends BaseModel
{
    use HashId;

    public $table = 'pireps';
    public $incrementing = false;

    public $fillable = [
        'id',
        'user_id',
        'airline_id',
        'aircraft_id',
        'flight_id',
        'flight_number',
        'route_code',
        'route_leg',
        'dpt_airport_id',
        'arr_airport_id',
        'level',
        'distance',
        'planned_distance',
        'flight_time',
        'planned_flight_time',
        'zfw',
        'block_fuel',
        'fuel_used',
        'landing_rate',
        'route',
        'notes',
        'source',
        'source_name',
        'flight_type',
        'state',
        'status',
        'raw_data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id'               => 'integer',
        'airline_id'            => 'integer',
        'aircraft_id'           => 'integer',
        'level'                 => 'integer',
        'distance'              => 'float',
        'planned_distance'      => 'float',
        'flight_time'           => 'integer',
        'planned_flight_time'   => 'integer',
        'zfw'                   => 'float',
        'block_fuel'            => 'float',
        'fuel_used'             => 'float',
        'landing_rate'          => 'float',
        'source'                => 'integer',
        'flight_type'           => 'integer',
        'state'                 => 'integer',
        'status'                => 'integer',
    ];

    public static $rules = [
        'airline_id'        => 'required|exists:airlines,id',
        'aircraft_id'       => 'required|exists:aircraft,id',
        'flight_number'     => 'required',
        'dpt_airport_id'    => 'required',
        'arr_airport_id'    => 'required',
        'notes'             => 'nullable',
        'route'             => 'nullable',
    ];

    /**
     * Get the flight ident, e.,g JBU1900
     * @return string
     */
    public function getIdentAttribute()
    {
        $flight_id = $this->airline->code;
        $flight_id .= $this->flight_number;

        if(filled($this->route_code)) {
            $flight_id .= '/C'.$this->route_code;
        }

        if(filled($this->route_leg)) {
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
        try {
            $distance = (float) $this->attributes['distance'];
            return new Distance($distance, 'mi');
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
            $this->attributes['distance'] = $value->toUnit(Distance::STORAGE_UNIT);
        } else {
            $this->attributes['distance'] = $value;
        }
    }

    /**
     * Return the planned_distance in a converter class
     * @return int|Distance
     */
    public function getPlannedDistanceAttribute()
    {
        try {
            $distance = (float) $this->attributes['planned_distance'];
            return new Distance($distance, 'mi');
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
    public function setPlannedDistanceAttribute($value)
    {
        if ($value instanceof Distance) {
            $this->attributes['distance'] = $value->toUnit(Distance::STORAGE_UNIT);
        } else {
            $this->attributes['distance'] = $value;
        }
    }

    /**
     * Do some cleanup on the route
     * @param $route
     */
    public function setRouteAttribute($route)
    {
        $route = strtoupper(trim($route));
        $this->attributes['route'] = $route;
    }

    /**
     * Check if this PIREP is allowed to be updated
     * @return bool
     */
    public function allowedUpdates()
    {
        if($this->state === PirepState::CANCELLED) {
            return false;
        }

        return true;
    }


    /**
     * Foreign Keys
     */

    public function acars()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
                    ->where('type', AcarsType::FLIGHT_PATH)
                    ->orderBy('created_at', 'asc');
    }

    public function acars_logs()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
                    ->where('type', AcarsType::LOG)
                    ->orderBy('created_at', 'asc');
    }

    public function acars_route()
    {
        return $this->hasMany(Acars::class, 'pirep_id')
                    ->where('type', AcarsType::ROUTE)
                    ->orderBy('order', 'asc');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo(Airport::class, 'arr_airport_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo(Airport::class, 'dpt_airport_id');
    }

    public function comments()
    {
        return $this->hasMany(PirepComment::class, 'pirep_id')
                ->orderBy('created_at', 'desc');
    }

    public function fields()
    {
        return $this->hasMany(PirepFieldValues::class, 'pirep_id');
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function pilot()
    {
        return $this->user();
    }

    /**
     * Relationship that holds the current position, but limits the ACARS
     *  relationship to only one row (the latest), to prevent an N+! problem
     */
    public function position()
    {
        return $this->hasOne(Acars::class, 'pirep_id')
                    ->where('type', AcarsType::FLIGHT_PATH)
                    ->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
