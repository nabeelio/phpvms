<?php
/**
 * Based on https://github.com/scottlaurent/accounting
 * With modifications for phpVMS
 */

namespace App\Models;

use App\Support\Money;
use Carbon\Carbon;

/**
 * Class Journal
 * @package Scottlaurent\Accounting
 * @property    Money $balance
 * @property    string $currency
 * @property    Carbon $updated_at
 * @property    Carbon $post_date
 * @property    Carbon $created_at
 */
class Journal extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'journals';

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'updated_at'
    ];

    /**
     * Get all of the morphed models.
     */
    public function morphed()
    {
        return $this->morphTo();
    }

    /**
     * @internal Journal $journal
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    protected static function boot()
    {
        static::created(function (Journal $journal) {
            $journal->resetCurrentBalances();
        });

        parent::boot();
    }

    /**
     * Relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    /**
     * Relationship
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
     * @return Journal
     */
    public function assignToLedger(Ledger $ledger)
    {
        $ledger->journals()->save($this);
        return $this;
    }

    /**
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function resetCurrentBalances()
    {
        $this->balance = $this->getBalance();
        $this->save();
    }

    /**
     * @param $value
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalanceAttribute($value): Money
    {
        return new Money($value);
    }

    /**
     * @param $value
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function setBalanceAttribute($value): void
    {
        $value = ($value instanceof Money)
            ? $value
            : new Money($value);

        $this->attributes['balance'] = $value ? (int)$value->getAmount() : null;
    }

    /**
     * Get the debit only balance of the journal based on a given date.
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDebitBalanceOn(Carbon $date): Money
    {
        $balance = $this->transactions()
            ->where('post_date', '<=', $date)
            ->sum('debit') ?: 0;

        return new Money($balance);
    }

    /**
     * @param Journal $object
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactionsReferencingObjectQuery($object)
    {
        return $this
            ->transactions()
            ->where('ref_class', \get_class($object))
            ->where('ref_class_id', $object->id);
    }

    /**
     * Get the credit only balance of the journal based on a given date.
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
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
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalanceOn(Carbon $date)
    {
        return $this->getCreditBalanceOn($date)
                    ->subtract($this->getDebitBalanceOn($date));
    }

    /**
     * Get the balance of the journal as of right now, excluding future transactions.
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getCurrentBalance()
    {
        return $this->getBalanceOn(Carbon::now());
    }

    /**
     * Get the balance of the journal.  This "could" include future dates.
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalance()
    {
        $balance = $this
                ->transactions()
                ->sum('credit') - $this->transactions()->sum('debit');

        return new Money($balance);
    }

    /**
     * Get the balance of the journal in dollars.  This "could" include future dates.
     * @return float|int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getCurrentBalanceInDollars()
    {
        return $this->getCurrentBalance()->getValue();
    }

    /**
     * Get balance
     * @return float|int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalanceInDollars()
    {
        return $this->getBalance()->getValue();
    }

    /**
     * @param $value
     * @param null $memo
     * @param null $post_date
     * @return JournalTransaction
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function credit($value, $memo = null, $post_date = null, $transaction_group = null)
    {
        $value = ($value instanceof Money)
            ? $value
            : new Money($value);

        return $this->post($value, null, $memo, $post_date, $transaction_group);
    }

    /**
     * @param $value
     * @param null $memo
     * @param null $post_date
     * @return JournalTransaction
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function debit($value, $memo = null, $post_date = null, $transaction_group = null)
    {
        $value = ($value instanceof Money)
            ? $value
            : new Money($value);

        return $this->post(null, $value, $memo, $post_date, $transaction_group);
    }

    /**
     * @param Money $credit
     * @param Money $debit
     * @param $memo
     * @param Carbon $post_date
     * @return JournalTransaction
     */
    private function post(Money $credit = null, Money $debit = null, $memo = null, $post_date = null, $transaction_group)
    {
        $transaction = new JournalTransaction();
        $transaction->credit = $credit ? $credit->getAmount() : null;
        $transaction->debit = $debit ? $debit->getAmount() : null;
        $currency_code = $credit
            ? $credit->getCurrency()->getCode()
            : $debit->getCurrency()->getCode();

        $transaction->memo = $memo;
        $transaction->currency = $currency_code;
        $transaction->post_date = $post_date ?: Carbon::now();
        $transaction->transaction_group = $transaction_group;

        $this->transactions()->save($transaction);

        return $transaction;
    }

    /**
     * Credit a journal by a given dollar amount
     * @param $value
     * @param null $memo
     * @param null $post_date
     * @return JournalTransaction
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function creditDollars($value, $memo = null, $post_date = null)
    {
        $value = Money::convertToSubunit($value);
        return $this->credit($value, $memo, $post_date);
    }

    /**
     * Debit a journal by a given dollar amount
     * @param $value
     * @param null $memo
     * @param null $post_date
     * @return JournalTransaction
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function debitDollars($value, $memo = null, $post_date = null)
    {
        $value = Money::convertToSubunit($value);
        return $this->debit($value, $memo, $post_date);
    }

    /**
     * Calculate the dollar amount debited to a journal today
     * @return float|int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDollarsDebitedToday()
    {
        $today = Carbon::now();
        return $this->getDollarsDebitedOn($today);
    }

    /**
     * Calculate the dollar amount credited to a journal today
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDollarsCreditedToday()
    {
        $today = Carbon::now();
        return $this->getDollarsCreditedOn($today);
    }

    /**
     * Calculate the dollar amount debited to a journal on a given day
     * @param Carbon $date
     * @return Money|float|int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDollarsDebitedOn(Carbon $date)
    {
        $amount = $this->transactions()
            ->whereBetween('post_date', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay()
            ])
            ->sum('debit');

        return new Money($amount);
    }

    /**
     * Calculate the dollar amount credited to a journal on a given day
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDollarsCreditedOn(Carbon $date)
    {
        $amount = $this
            ->transactions()
            ->whereBetween('post_date', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay()
            ])
            ->sum('credit');

        return new Money($amount);
    }
}
