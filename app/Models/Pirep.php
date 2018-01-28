<?php

namespace App\Models;

use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Traits\HashId;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Pirep
 *
 * @package App\Models
 */
class Pirep extends BaseModel
{
    use HashId;
    use SoftDeletes;

    public $table = 'pireps';
    public $incrementing = false;

    protected $dates = ['deleted_at'];

    public $fillable = [
        'user_id',
        'flight_id',
        'flight_number',
        'route_code',
        'route_leg',
        'airline_id',
        'aircraft_id',
        'altitude',
        'flight_time',
        'planned_flight_time',
        'dpt_airport_id',
        'arr_airport_id',
        'fuel_used',
        'source',
        'level',
        'route',
        'notes',
        'state',
        'status',
        'raw_data',
    ];

    protected $casts = [
        'user_id'               => 'integer',
        'flight_time'           => 'integer',
        'planned_flight_time'   => 'integer',
        'level'                 => 'integer',
        'altitude'              => 'integer',
        'fuel_used'             => 'float',
        'gross_weight'          => 'float',
        'source'                => 'integer',
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
     * Check if this PIREP is allowed to be updated
     * @return bool
     */
    public function allowedUpdates()
    {
        if ($this->state === PirepState::CANCELLED) {
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
