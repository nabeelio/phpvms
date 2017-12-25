<?php

namespace App\Models;

/**
 * Class Subfleet
 * @package App\Models
 */
class Subfleet extends BaseModel
{
    public $table = 'subfleets';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'airline_id',
        'name',
        'type',
        'fuel_type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'airline_id' => 'integer',
        'fuel_type' => 'integer',
        'cargo_capacity' => 'double',
        'fuel_capacity' => 'double',
        'gross_weight' => 'double',
    ];

    public function aircraft()
    {
        return $this->hasMany('App\Models\Aircraft', 'subfleet_id');
    }

    public function airline()
    {
        return $this->belongsTo('App\Models\Airline', 'airline_id');
    }

    public function fares()
    {
        return $this->belongsToMany(
            'App\Models\Fare',
            'subfleet_fare'
        )->withPivot('price', 'cost', 'capacity');
    }

    public function flights()
    {
        return $this->belongsToMany('App\Models\Flight', 'subfleet_flight');
    }

    public function ranks()
    {
        return $this->belongsToMany(
            'App\Models\Ranks',
            'subfleet_rank'
        )->withPivot('acars_pay', 'manual_pay');
    }
}
