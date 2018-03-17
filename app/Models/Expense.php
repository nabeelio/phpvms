<?php

namespace App\Models;

/**
 * Class Expense
 * @property int airline_id
 * @property float amount
 * @property string name
 * @property string ref_class
 * @property string ref_class_id
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
     * Get the referring object
     */
    public function getReference()
    {
        if (!$this->ref_class || !$this->ref_class_id) {
            return null;
        }

        if($this->ref_class === __CLASS__) {
            return $this;
        }

        try {
            $klass = new $this->ref_class;
            return $klass->find($this->ref_class_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Foreign Keys
     */

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }
}
