<?php

namespace App\Models;

use App\Models\Enums\AcarsType;
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
        'id'                    => 'string',
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
        'flight_number' => 'required',
        'dpt_airport_id' => 'required',
        'arr_airport_id' => 'required',
        'notes' => 'nullable',
        'route' => 'nullable',
    ];

    /**
     * Get the flight ident, e.,g JBU1900
     * @return string
     */
    public function getIdentAttribute()
    {
        $flight_id = $this->airline->code;
        if(!empty($this->flight_number)) {
            $flight_id .= $this->flight_number;
        } else {
            if ($this->flight_id) {
                $flight_id .= $this->flight->flight_number;
            }
        }

        return $flight_id;
    }

    /**
     * Foreign Keys
     */

    public function acars()
    {
        return $this->hasMany('App\Models\Acars', 'pirep_id')
                    ->where('type', AcarsType::FLIGHT_PATH)
                    ->orderBy('created_at', 'asc');
    }

    public function acars_route()
    {
        return $this->hasMany('App\Models\Acars', 'pirep_id')
                    ->where('type', AcarsType::ROUTE)
                    ->orderBy('created_at', 'asc');
    }

    public function aircraft()
    {
        return $this->belongsTo('App\Models\Aircraft', 'aircraft_id');
    }

    public function airline()
    {
        return $this->belongsTo('App\Models\Airline', 'airline_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'arr_airport_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'dpt_airport_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\PirepComment', 'pirep_id')
                ->orderBy('created_at', 'desc');
    }

    public function fields()
    {
        return $this->hasMany('App\Models\PirepFieldValues', 'pirep_id');
    }

    public function flight()
    {
        return $this->belongsTo('App\Models\Flight', 'flight_id');
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
        return $this->hasOne('App\Models\Acars', 'pirep_id')
                    ->where('type', AcarsType::FLIGHT_PATH)
                    ->latest();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
