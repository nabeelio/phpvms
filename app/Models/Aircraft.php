<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;
use Carbon\Carbon;
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
    use ExpensableTrait;
    use FilesTrait;
    use BelongsToThrough;

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
        'status',
        'state',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'subfleet_id' => 'integer',
        'mtow'        => 'float',
        'zfw'         => 'float',
        'flight_time' => 'float',
        'state'       => 'integer',
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
     * @return string
     */
    public function getIdentAttribute(): string
    {
        return $this->registration.' ('.$this->icao.')';
    }

    /**
     * See if this aircraft is active
     *
     * @return bool
     */
    public function getActiveAttribute(): bool
    {
        return $this->status === AircraftStatus::ACTIVE;
    }

    /**
     * Capitalize the ICAO when set
     *
     * @param $icao
     */
    public function setIcaoAttribute($icao): void
    {
        $this->attributes['icao'] = strtoupper($icao);
    }

    /**
     * Return the landing time in carbon format if provided
     *
     * @return Carbon|null
     */
    public function getLandingTimeAttribute()
    {
        if (array_key_exists('landing_time', $this->attributes) && filled($this->attributes['landing_time'])) {
            return new Carbon($this->attributes['landing_time']);
        }
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
