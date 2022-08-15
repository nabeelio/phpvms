<?php
/**
 * Based on https://github.com/scottlaurent/accounting
 * With modifications for phpVMS
 */

namespace App\Models;

use App\Contracts\Model;
use App\Models\Casts\MoneyCast;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Holds various journals, depending on the morphed_type and morphed_id columns
 *
 * @property mixed                         id
 * @property Money  $balance
 * @property string $currency
 * @property Carbon $updated_at
 * @property Carbon $post_date
 * @property Carbon $created_at
 * @property \App\Models\Enums\JournalType type
 * @property mixed                         morphed_type
 * @property mixed                         morphed_id
 */
class Journal extends Model
{
    use HasFactory;

    protected $table = 'journals';

    protected $fillable = [
        'ledger_id',
        'journal_type',
        'balance',
        'currency',
        'morphed_type',
        'morphed_id',
    ];

    public $casts = [
        'balance' => MoneyCast::class,
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    /**
     * Get all of the morphed models.
     */
    public function morphed()
    {
        return $this->morphTo();
    }

    /**
     * Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(JournalTransaction::class);
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param Ledger $ledger
     *
     * @return Journal
     */
    public function assignToLedger(Ledger $ledger)
    {
        $ledger->journals()->save($this);

        return $this;
    }

    /**
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function resetCurrentBalances()
    {
        $this->balance = $this->getBalance();
        $this->save();
    }

    /**
     * @param Journal $object
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactionsReferencingObjectQuery($object)
    {
        return $this
            ->transactions()
            ->where('ref_model', \get_class($object))
            ->where('ref_model_id', $object->id);
    }

    /**
     * Get the credit only balance of the journal based on a given date.
     *
     * @param Carbon $date
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return Money
     */
    public function getCreditBalanceOn(Carbon $date)
    {
        $balance = $this->transactions()
            ->where('post_date', '<=', $date)
            ->sum('credit') ?: 0;

        return new Money($balance);
    }

    /**
     * Get the balance of the journal based on a given date.
     *
     * @param Carbon $date
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return Money
     */
    public function getBalanceOn(Carbon $date)
    {
        return $this->getCreditBalanceOn($date)
            ->subtract($this->getDebitBalanceOn($date));
    }

    /**
     * Get the balance of the journal as of right now, excluding future transactions.
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return Money
     */
    public function getCurrentBalance()
    {
        return $this->getBalanceOn(Carbon::now('UTC'));
    }

    /**
     * Get the balance of the journal.  This "could" include future dates.
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return Money
     */
    public function getBalance()
    {
        $balance = $this
                ->transactions()
                ->sum('credit') - $this->transactions()->sum('debit');

        return new Money($balance);
    }
}
