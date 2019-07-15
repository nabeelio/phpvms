<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * Class Fare
 *
 * @property int capacity
 * @property float   cost
 * @property float   price
 * @property mixed   code
 * @property mixed   count Only when merged with pivot
 */
class Fare extends Model
{
    public $table = 'fares';

    protected $fillable = [
        'code',
        'name',
        'price',
        'cost',
        'capacity',
        'notes',
        'active',
    ];

    protected $casts = [
        'price'    => 'float',
        'cost'     => 'float',
        'capacity' => 'integer',
        'active'   => 'boolean',
    ];

    public static $rules = [
        'code' => 'required',
        'name' => 'required',
    ];

    /**
     * any foreign keys
     */
    public function subfleets()
    {
        return $this->belongsToMany(Subfleet::class, 'subfleet_fare')
            ->withPivot('price', 'cost', 'capacity');
    }
}
