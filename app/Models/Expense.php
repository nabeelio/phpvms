<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\ReferenceTrait;

/**
 * @property int    airline_id
 * @property float  amount
 * @property string name
 * @property string type
 * @property string flight_type
 * @property string ref_model
 * @property string ref_model_id
 * @property bool   charge_to_user
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Expense extends Model
{
    use ReferenceTrait;

    public $table = 'expenses';

    protected $fillable = [
        'airline_id',
        'name',
        'amount',
        'type',
        'flight_type',
        'multiplier',
        'charge_to_user',
        'ref_model',
        'ref_model_id',
        'active',
    ];

    public static $rules = [
        'active'         => 'bool',
        'airline_id'     => 'integer',
        'amount'         => 'float',
        'multiplier'     => 'bool',
        'charge_to_user' => 'bool',
    ];

    /**
     * flight_type is stored a comma delimited list in table. Retrieve it as an array
     *
     * @return array
     */
    public function getFlightTypeAttribute()
    {
        if (empty(trim($this->attributes['flight_type']))) {
            return [];
        }

        return explode(',', $this->attributes['flight_type']);
    }

    /**
     * Make sure the flight type is stored a comma-delimited list in the table
     *
     * @param string $value
     */
    public function setFlightTypeAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['flight_type'] = implode(',', $value);
        } else {
            $this->attributes['flight_type'] = trim($value);
        }
    }

    /**
     * Foreign Keys
     */
    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function ref_model()
    {
        return $this->morphTo();
    }
}
