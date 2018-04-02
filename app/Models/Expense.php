<?php

namespace App\Models;
use App\Interfaces\Model;
use App\Models\Traits\ReferenceTrait;

/**
 * Class Expense
 * @property int    airline_id
 * @property float  amount
 * @property string name
 * @property string ref_model
 * @property string ref_model_id
 * @package App\Models
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
        'multiplier',
        'charge_to_user',
        'ref_model',
        'ref_model_id',
        'active',
    ];

    public static $rules = [
        'active'         => 'boolean',
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
}
