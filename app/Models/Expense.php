<?php

namespace App\Models;

/**
 * Class Expense
 * @property float amount
 * @property string name
 * @package App\Models
 */
class Expense extends BaseModel
{
    public $table = 'expenses';

    public $fillable = [
        'airline_id',
        'name',
        'amount',
        'type',
        'multiplier',
        'charge_to_user',
        'ref_class',
        'ref_class_id',
        'active',
    ];

    public static $rules = [
        'active'          => 'boolean',
        'airline_id'      => 'integer',
        'amount'          => 'float',
        'type'            => 'integer',
        'multiplier'      => 'bool',
        'charge_to_user'  => 'bool',
    ];

    /**
     * Foreign Keys
     */

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }
}
