<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class FlightFieldValue
 * @package App\Models
 */
class FlightFieldValue extends Model
{
    public $table = 'flight_field_values';

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
