<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class FlightFields
 * @package App\Models
 */
class FlightFields extends Model
{
    public $table = 'flight_fields';

    public $fillable = [
        'flight_id',
        'name',
        'value',
    ];

    public static $rules = [];

    /**
     * Relationships
     */

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }
}
