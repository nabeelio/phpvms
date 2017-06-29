<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Pirep
 *
 * @package App\Models
 */
class Pirep extends Model
{
    use Uuids;
    use SoftDeletes;

    public $table = 'pireps';

    protected $dates = ['deleted_at'];

    public $fillable
        = [
            'user_id',
            'flight_id',
            'aircraft_id',
            'flight_time',
            'route_code',
            'route_leg',
            'dpt_airport_id',
            'arr_airport_id',
            'level',
            'route',
            'notes',
            'status',
            'raw_data',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'user_id'     => 'integer',
            'flight_id'   => 'string',
            'aircraft_id' => 'integer',
            'flight_time' => 'integer',
            'level'       => 'integer',
            'route'       => 'string',
            'notes'       => 'string',
            'raw_data'    => 'string',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'dpt_airport_id' => 'required',
            'arr_airport_id' => 'required',
        ];

    /**
     * Foreign Keys
     */

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function flight()
    {
        return $this->belongsTo('App\Models\Flight', 'flight_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'dpt_airport_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'arr_airport_id');
    }

}
