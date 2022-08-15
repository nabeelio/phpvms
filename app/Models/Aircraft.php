<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Casts\FuelCast;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * @property int      id
 * @property mixed    subfleet_id
 * @property string   airport_id   The apt where the aircraft is
 * @property string   hub_id       The apt where the aircraft is based
 * @property string   ident
 * @property string   name
 * @property string   icao
 * @property string   registration
 * @property int      flight_time
 * @property float    mtow
 * @property float    zfw
 * @property string   hex_code
 * @property Airport  airport
 * @property Airport  hub
 * @property Subfleet subfleet
 * @property int      status
 * @property int      state
 * @property Carbon   landing_time
 * @property float    fuel_onboard
 */
class Aircraft extends Model
{
    use BelongsToThrough;
    use ExpensableTrait;
    use FilesTrait;
    use HasFactory;

    public $table = 'aircraft';

    protected $fillable = [
        'subfleet_id',
        'airport_id',
        'hub_id',
        'iata',
        'icao',
        'name',
        'registration',
        'hex_code',
        'flight_time',
        'mtow',
        'zfw',
        'fuel_onboard',
        'status',
        'state',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'subfleet_id'  => 'integer',
        'mtow'         => 'float',
        'zfw'          => 'float',
        'flight_time'  => 'float',
        'fuel_onboard' => FuelCast::class,
        'state'        => 'integer',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'subfleet_id'  => 'required',
        'name'         => 'required',
        'status'       => 'required',
        'registration' => 'required',
        'mtow'         => 'nullable|numeric',
        'zfw'          => 'nullable|numeric',
    ];

    /**
     * @return Attribute
     */
    public function active(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attr) => $attr['status'] === AircraftStatus::ACTIVE
        );
    }

    /**
     * @return Attribute
     */
    public function icao(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper($value)
        );
    }

    /**
     * @return Attribute
     */
    public function ident(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attrs) => $attrs['registration'].' ('.$attrs['icao'].')'
        );
    }

    /**
     * Return the landing time
     *
     * @return Attribute
     */
    public function landingTime(): Attribute
    {
        return Attribute::make(
            get: function ($_, $attrs) {
                if (array_key_exists('landing_time', $attrs) && filled($attrs['landing_time'])) {
                    return new Carbon($attrs['landing_time']);
                }

                return $attrs['landing_time'];
            }
        );
    }

    /**
     * foreign keys
     */
    public function airline()
    {
        return $this->belongsToThrough(Airline::class, Subfleet::class);
    }

    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id');
    }

    public function hub()
    {
        return $this->hasOne(Airport::class, 'id', 'hub_id');
    }

    public function pireps()
    {
        return $this->hasMany(Pirep::class, 'aircraft_id');
    }

    public function simbriefs()
    {
        return $this->hasMany(SimBrief::class, 'aircraft_id');
    }

    public function subfleet()
    {
        return $this->belongsTo(Subfleet::class, 'subfleet_id');
    }
}
