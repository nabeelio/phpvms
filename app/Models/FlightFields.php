<?php

namespace App\Models;

/**
 * Class Flight
 *
 * @package App\Models
 */
class FlightFields extends BaseModel
{
    public $table = 'flight_fields';

    public $fillable = [
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
