<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * Class FlightFieldValue
 *
 * @property string   flight_id
 * @property string   name
 * @property string   value
 */
class FlightFieldValue extends Model
{
    public $table = 'flight_field_values';

    protected $fillable = [
        'flight_id',
        'name',
        'slug',
        'value',
    ];

    public static $rules = [];

    /**
     * @param $name
     */
    public function setNameAttribute($name): void
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str_slug($name);
    }

    /**
     * Relationships
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }
}
