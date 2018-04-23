<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class FlightFieldValue
 * @property string   flight_id
 * @property string   name
 * @property string   value
 * @package App\Models
 */
class FlightFieldValue extends Model
{
    public $table = 'flight_field_values';

    protected $fillable = [
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
