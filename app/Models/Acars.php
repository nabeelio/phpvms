<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;

/**
 * @property string id
 * @property string pirep_id
 * @property int    type
 * @property string name
 * @property float  lat
 * @property float  lon
 * @property float  altitude
 * @property int    gs
 * @property int    heading
 * @property int    order
 * @property int    nav_type
 */
class Acars extends Model
{
    use HashIdTrait;

    public $table = 'acars';

    protected $keyType = 'string';
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

    public static $rules = [
        'pirep_id' => 'required',
    ];

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
