<?php
/**
 * Based on https://github.com/scottlaurent/accounting
 * With modifications for phpVMS
 */

namespace App\Models;

/**
 * @property string ref_class
 * @property string ref_class_id
 * @property string currency
 * @property string memo
 * @property string transaction_group
 * @property static post_date
 * @property integer credit
 * @property integer debit
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

    /**
     *
     */
    protected static function boot()
    {
        static::creating(function ($transaction) {
            $transaction->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        });

        static::saved(function ($transaction) {
            $transaction->journal->resetCurrentBalances();
        });

        static::deleted(function ($transaction) {
            $transaction->journal->resetCurrentBalances();
        });

        parent::boot();
    }

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
            $_class = new $this->ref_class;
            return $_class->find($this->ref_class_id);
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
