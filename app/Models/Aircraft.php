<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;
use Carbon\Carbon;

/**
 * @property int      id
 * @property mixed    subfleet_id
 * @property string   airport_id   The apt where the aircraft is
 * @property string   ident
 * @property string   name
 * @property string   icao
 * @property string   registration
 * @property int      flight_time
 * @property float    mtow
 * @property float    zfw
 * @property string   hex_code
 * @property Airport  airport
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

    public $table = 'aircraft';

    protected $fillable = [
        'subfleet_id',
        'airport_id',
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
     * foreign keys
     */
    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id');
    }

    public function subfleet()
    {
        return $this->belongsTo(Subfleet::class, 'subfleet_id');
    }
}
