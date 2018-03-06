<?php

namespace App\Models;

use App\Models\Enums\AircraftStatus;
use App\Models\Traits\Expensable;

/**
 * Class Subfleet
 * @package App\Models
 */
class Subfleet extends BaseModel
{
    use Expensable;

    public $table = 'subfleets';

    public $fillable = [
        'airline_id',
        'name',
        'type',
        'fuel_type',
        'ground_handling_multiplier',
        'cargo_capacity',
        'fuel_capacity',
        'gross_weight',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'airline_id'                  => 'integer',
        'fuel_type'                   => 'integer',
        'ground_handling_multiplier'  => 'float',
        'cargo_capacity'              => 'float',
        'fuel_capacity'               => 'float',
        'gross_weight'                => 'float',
    ];

    public static $rules = [
        'name'                        => 'required',
        'type'                        => 'required',
        'ground_handling_multiplier'  => 'nullable|numeric',
    ];

    /**
     * Modify some fields on the fly. Make sure the subfleet names don't
     * have spaces in them, so the csv import/export can use the types
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (filled($model->type)) {
                $model->type = str_replace(' ', '-', $model->type);
                $model->type = str_replace(',', '', $model->type);
            }

            if(!filled($model->ground_handling_multiplier)) {
                $model->ground_handling_multiplier = 100;
            }
        });

        static::updating(function ($model) {
            if (filled($model->type)) {
                $model->type = str_replace(' ', '-', $model->type);
                $model->type = str_replace(',', '', $model->type);
            }
        });
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
        return $this->belongsToMany(Flight::class, 'subfleet_flight');
    }

    public function ranks()
    {
        return $this->belongsToMany(Rank::class, 'subfleet_rank')
                    ->withPivot('acars_pay', 'manual_pay');
    }
}
