<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Flight
 *
 * @package App\Models
 */
class FlightFields extends Model
{

    public $table = 'flight_fields';

    protected $dates = ['deleted_at'];

    public $fillable
        = [
            'flight_id',
            'name',
            'value',
        ];

    protected $casts = [];

    public static $rules = [];

    /**
     * Relationships
     */

    public function flight()
    {
        return $this->belongsTo('App\Models\Flight', 'flight_id');
    }

}
