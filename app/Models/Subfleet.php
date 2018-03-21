<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;

/**
 * Class Subfleet
 * @property int     id
 * @property string  type
 * @property string  ground_handling_multiplier
 * @package App\Models
 */
class Subfleet extends Model
{
    use ExpensableTrait;

    public $table = 'subfleets';

    public $fillable = [
        'airline_id',
        'type',
        'name',
        'fuel_type',
        'ground_handling_multiplier',
        'cargo_capacity',
        'fuel_capacity',
        'gross_weight',
    ];

    public $casts = [
        'airline_id'                 => 'integer',
        'fuel_type'                  => 'integer',
        'ground_handling_multiplier' => 'float',
        'cargo_capacity'             => 'float',
        'fuel_capacity'              => 'float',
        'gross_weight'               => 'float',
    ];

    public static $rules = [
        'type'                       => 'required|unique:subfleet',
        'name'                       => 'required',
        'ground_handling_multiplier' => 'nullable|numeric',
    ];

    /**
     * @param $type
     */
    public function setTypeAttribute($type)
    {
        $type = str_replace(' ', '-', $type);
        $type = str_replace(',', '', $type);

        $this->attributes['type'] = $type;
    }

    /**
     * Relationships
     */

    /**
     * @return $this
     */
    public function aircraft()
    {
        return $this->hasMany(Aircraft::class, 'subfleet_id')
            ->where('status', AircraftStatus::ACTIVE);
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function fares()
    {
        return $this->belongsToMany(Fare::class, 'subfleet_fare')
            ->withPivot('price', 'cost', 'capacity');
    }

    public function flights()
    {
        return $this->belongsToMany(Flight::class, 'flight_subfleet');
    }

    public function ranks()
    {
        return $this->belongsToMany(Rank::class, 'subfleet_rank')
            ->withPivot('acars_pay', 'manual_pay');
    }
}
