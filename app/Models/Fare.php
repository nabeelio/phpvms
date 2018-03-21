<?php

namespace App\Models;
use App\Interfaces\Model;

/**
 * Class Fare
 * @property integer capacity
 * @property float   cost
 * @property float   price
 * @property mixed   code
 * @package App\Models
 */
class Fare extends Model
{
    public $table = 'fares';

    public $fillable = [
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
        'code' => 'required|unique',
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
