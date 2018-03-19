<?php
/**
 * Based on https://github.com/scottlaurent/accounting
 * With modifications for phpVMS
 */

namespace App\Models;

/**
 * @property string id  UUID type
 * @property string  currency
 * @property string  memo
 * @property string  transaction_group
 * @property string  post_date
 * @property integer credit
 * @property integer debit
 * @property string  ref_class
 * @property integer ref_class_id
 * @property Journal journal
 */
class JournalTransaction extends BaseModel
{
    protected $table = 'journal_transactions';
    public $incrementing = false;

    public $fillable = [
        'transaction_group',
        'journal_id',
        'credit',
        'debit',
        'currency',
        'memo',
        'tags',
        'ref_class',
        'ref_class_id',
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
     * @param BaseModel $object
     * @return JournalTransaction
     */
    public function referencesObject($object)
    {
        $this->ref_class = \get_class($object);
        $this->ref_class_id = $object->id;
        $this->save();
        return $this;
    }

    /**
     *
     */
    public function getReferencedObject()
    {
        if ($classname = $this->ref_class) {
            $klass = new $this->ref_class;
            return $klass->find($this->ref_class_id);
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
