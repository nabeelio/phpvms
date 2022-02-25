<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    use HasFactory;

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
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function distance(): Attribute
    {
        return new Attribute(
            set: function ($value) {
                if ($value instanceof Distance) {
                    return $value->toUnit(config('phpvms.internal_units.distance'));
                }

                return $value;
            }
        );
    }

    /**
     * Set the amount of fuel
     *
     * @return Attribute
     */
    public function fuel(): Attribute
    {
        return new Attribute(
            set: function ($value) {
                if ($value instanceof Fuel) {
                    return $value->toUnit(config('phpvms.internal_units.fuel'));
                }

                return $value;
            }
        );
    }

    /**
     * FKs
     */
    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
