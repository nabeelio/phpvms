<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Casts\CommaDelimitedCast;
use App\Models\Traits\ReferenceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int     airline_id
 * @property float   amount
 * @property string  name
 * @property string  type
 * @property string  flight_type
 * @property string  ref_model
 * @property string  ref_model_id
 * @property bool    charge_to_user
 * @property Airline $airline
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Expense extends Model
{
    use HasFactory;
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

    public $casts = [
        'flight_type' => CommaDelimitedCast::class,
    ];

    public static $rules = [
        'active'         => 'bool',
        'airline_id'     => 'integer',
        'amount'         => 'float',
        'multiplier'     => 'bool',
        'charge_to_user' => 'bool',
    ];

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
