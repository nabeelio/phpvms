<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string  name
 * @property float   cost
 * @property float   price
 * @property int     code
 * @property int     capacity
 * @property int     count Only when merged with pivot
 * @property int     type
 * @property string  notes
 * @property bool    active
 */
class Fare extends Model
{
    use HasFactory;

    public $table = 'fares';

    protected $fillable = [
        'id',
        'code',
        'name',
        'type',
        'price',
        'cost',
        'capacity',
        'count',
        'notes',
        'active',
    ];

    protected $casts = [
        'price'    => 'float',
        'cost'     => 'float',
        'capacity' => 'integer',
        'count'    => 'integer',
        'type'     => 'integer',
        'active'   => 'boolean',
    ];

    public static $rules = [
        'code' => 'required',
        'name' => 'required',
        'type' => 'required',
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
