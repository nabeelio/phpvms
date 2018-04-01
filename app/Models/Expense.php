<?php

namespace App\Models;
use App\Interfaces\Model;

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
        'type'           => 'integer',
        'multiplier'     => 'bool',
        'charge_to_user' => 'bool',
    ];

    /**
     * Get the referring object
     */
    public function getReference()
    {
        if (!$this->ref_model || !$this->ref_model_id) {
            return null;
        }

        if ($this->ref_model === __CLASS__) {
            return $this;
        }

        try {
            $klass = new $this->ref_model;

            return $klass->find($this->ref_model_id);
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
