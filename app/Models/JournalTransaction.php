<?php
/**
 * Based on https://github.com/scottlaurent/accounting
 * With modifications for phpVMS
 */

namespace App\Models;

use App\Interfaces\Model;

/**
 * @property string  id  UUID type
 * @property string  currency
 * @property string  memo
 * @property string  transaction_group
 * @property string  post_date
 * @property integer credit
 * @property integer debit
 * @property string  ref_model
 * @property integer ref_model_id
 * @property Journal journal
 */
class JournalTransaction extends Model
{
    protected $table = 'journal_transactions';

    public $incrementing = false;

    protected $fillable = [
        'transaction_group',
        'journal_id',
        'credit',
        'debit',
        'currency',
        'memo',
        'tags',
        'ref_model',
        'ref_model_id',
        'post_date'
    ];

    protected $casts = [
        'credits'   => 'integer',
        'debit'     => 'integer',
        'post_date' => 'datetime',
        'tags'      => 'array',
    ];

    //protected $dateFormat = 'Y-m-d';
    protected $dates = [
        'created_at',
        'updated_at',
        'post_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * @param Model $object
     * @return JournalTransaction
     */
    public function referencesObject($object)
    {
        $this->ref_model = \get_class($object);
        $this->ref_model_id = $object->id;
        $this->save();

        return $this;
    }

    /**
     *
     */
    public function getReferencedObject()
    {
        if ($classname = $this->ref_model) {
            $klass = new $this->ref_model;

            return $klass->find($this->ref_model_id);
        }

        return false;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
