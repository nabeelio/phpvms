<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

/**
 * Class Acars
 *
 * @param string id
 *
 * @property string pirep_id
 * @property int    type
 * @property string name
 * @property float lat
 * @property float lon
 * @property float altitude
 * @property int    gs
 * @property int    heading
 * @property int    order
 * @property int    nav_type
 */
class Acars extends Model
{
    use HashIdTrait;

    public $table = 'acars';
    public $incrementing = false;

    public $fillable = [
        'id',
        'pirep_id',
        'type',
        'nav_type',
        'order',
        'name',
        'status',
        'log',
        'lat',
        'lon',
        'distance',
        'heading',
        'altitude',
        'vs',
        'gs',
        'transponder',
        'autopilot',
        'fuel_flow',
        'sim_time',
        'created_at',
        'updated_at',
    ];

    public $casts = [
        'type'        => 'integer',
        'order'       => 'integer',
        'nav_type'    => 'integer',
        'lat'         => 'float',
        'lon'         => 'float',
        'distance'    => 'integer',
        'heading'     => 'integer',
        'altitude'    => 'float',
        'vs'          => 'float',
        'gs'          => 'float',
        'transponder' => 'integer',
        'fuel'        => 'float',
        'fuel_flow'   => 'float',
    ];
    /*public static $sanitize = [
        'sim_time' => 'carbon',
        'created_at' => '',
    ];*/

    public static $rules = [
        'pirep_id' => 'required',
    ];

    /**
     * Return a new Length unit so conversions can be made
     *
     * @param mixed $value
     *
     * @return int|Distance
     */
    /*public function getDistanceAttribute()
    {
        if (!array_key_exists('distance', $this->attributes)) {
            return 0;
        }

        try {
            $distance = (float) $this->attributes['distance'];
            if ($this->skip_mutator) {
                return $distance;
            }

            return new Distance($distance, config('phpvms.internal_units.distance'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }*/

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
     * Return a new Fuel unit so conversions can be made
     *
     * @param mixed $value
     *
     * @return int|Fuel
     */
    /*public function getFuelAttribute()
    {
        if (!array_key_exists('fuel', $this->attributes)) {
            return 0;
        }

        try {
            $fuel = (float) $this->attributes['fuel'];
            return new Fuel($fuel, config('phpvms.internal_units.fuel'));
        } catch (NonNumericValue $e) {
            return 0;
        } catch (NonStringUnitName $e) {
            return 0;
        }
    }*/

    /**
     * Set the amount of fuel
     *
     * @param $value
     */
    public function setFuelAttribute($value)
    {
        if ($value instanceof Fuel) {
            $this->attributes['fuel'] = $value->toUnit(
                config('phpvms.internal_units.fuel')
            );
        } else {
            $this->attributes['fuel'] = $value;
        }
    }

    /**
     * FKs
     */
    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
