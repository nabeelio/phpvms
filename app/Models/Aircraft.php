<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;

/**
 * @property int      id
 * @property mixed    subfleet_id
 * @property string   name
 * @property string   icao
 * @property string   registration
 * @property string   hex_code
 * @property Airport  airport
 * @property Subfleet subfleet
 * @property int      status
 * @property int      state
 * @package App\Models
 */
class Aircraft extends Model
{
    use ExpensableTrait;

    public $table = 'aircraft';

    protected $fillable = [
        'subfleet_id',
        'airport_id',
        'iata',
        'icao',
        'name',
        'registration',
        'hex_code',
        'zfw',
        'status',
        'state',
    ];

    /**
     * The attributes that should be casted to native types.
     */
    protected $casts = [
        'subfleet_id' => 'integer',
        'zfw'         => 'float',
        'status'      => 'integer',
        'state'       => 'integer',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'subfleet_id'  => 'required',
        'name'         => 'required',
        'registration' => 'required',
    ];

    /**
     * See if this aircraft is active
     * @return bool
     */
    public function getActiveAttribute(): bool
    {
        return $this->status === AircraftStatus::ACTIVE;
    }

    /**
     * Capitalize the ICAO when set
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
