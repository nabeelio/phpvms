<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\FareType;

/**
 * @property int       id
 * @property string    pirep_id
 * @property int       fare_id
 * @property string    code
 * @property string    name
 * @property int       count
 * @property float     price
 * @property float $cost
 * @property int   $capacity
 * @property Pirep     pirep
 * @property Fare|null fare
 * @property FareType  type
 */
class PirepFare extends Model
{
    public $table = 'pirep_fares';

    public $timestamps = false;

    protected $fillable = [
        'pirep_id',
        'fare_id',
        'code',
        'name',
        'count',
        'price',
        'cost',
        'capacity',
        'type',
    ];

    protected $casts = [
        'count'    => 'integer',
        'price'    => 'float',
        'cost'     => 'float',
        'capacity' => 'integer',
        'type'     => 'integer',
    ];

    public static $rules = [
        'count' => 'required',
    ];

    /**
     * Relationships
     */
    public function fare()
    {
        return $this->belongsTo(Fare::class, 'fare_id');
    }

    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
