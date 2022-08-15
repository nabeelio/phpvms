<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\AircraftStatus;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int     id
 * @property string  type
 * @property string  simbrief_type
 * @property string  name
 * @property int     airline_id
 * @property int     hub_id
 * @property string  ground_handling_multiplier
 * @property Fare[]  fares
 * @property float   cost_block_hour
 * @property float   cost_delay_minute
 * @property Airline airline
 * @property Airport hub
 * @property int     fuel_type
 */
class Subfleet extends Model
{
    use ExpensableTrait;
    use FilesTrait;
    use HasFactory;

    public $fillable = [
        'airline_id',
        'hub_id',
        'type',
        'simbrief_type',
        'name',
        'fuel_type',
        'cost_block_hour',
        'cost_delay_minute',
        'ground_handling_multiplier',
        'cargo_capacity',
        'fuel_capacity',
        'gross_weight',
    ];

    public $table = 'subfleets';

    public $casts = [
        'airline_id'                 => 'integer',
        'turn_time'                  => 'integer',
        'cost_block_hour'            => 'float',
        'cost_delay_minute'          => 'float',
        'fuel_type'                  => 'integer',
        'ground_handling_multiplier' => 'float',
        'cargo_capacity'             => 'float',
        'fuel_capacity'              => 'float',
        'gross_weight'               => 'float',
    ];

    public static $rules = [
        'type'                       => 'required',
        'name'                       => 'required',
        'hub_id'                     => 'nullable',
        'ground_handling_multiplier' => 'nullable|numeric',
    ];

    /**
     * @return Attribute
     */
    public function type(): Attribute
    {
        return Attribute::make(
            set: fn ($type) => str_replace([' ', ','], ['-', ''], $type)
        );
    }

    /**
     * Relationships
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

    public function hub()
    {
        return $this->hasOne(Airport::class, 'id', 'hub_id');
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

    public function typeratings()
    {
        return $this->belongsToMany(Typerating::class, 'typerating_subfleet', 'subfleet_id', 'typerating_id');
    }
}
