<?php

namespace App\Repositories;

use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Validator\Exceptions\ValidatorException;


class JournalRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return JournalTransaction::class;
    }

    /**
     * Post a new transaction to a journal, and also adjust the balance
     * on the transaction itself. A cron will run to reconcile the journal
     * balance nightly, since they're not atomic operations
     *
     * @param Journal       $journal
     * @param Money|null    $credit Amount to credit
     * @param Money|null    $debit Amount to debit
     * @param Model|null    $reference The object this is a reference to
     * @param string|null   $memo Memo for this transaction
     * @param string|null   $post_date Date of the posting
     * @param string|null   $transaction_group
     * @return mixed
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws ValidatorException
     */
    public function post(
        Journal $journal,
        Money $credit = null,
        Money $debit = null,
        $reference = null,
        $memo = null,
        $post_date = null,
        $transaction_group = null
    ) {

        $attrs = [
            'journal_id'        => $journal->id,
            'credit'            => $credit ? $credit->getAmount():null,
            'debit'             => $debit ? $debit->getAmount():null,
            'currency_code'     => config('phpvms.currency'),
            'memo'              => $memo,
            'post_date'         => $post_date ?: Carbon::now(),
            'transaction_group' => $transaction_group,
        ];

        if($reference !== null) {
            $attrs['ref_class'] = \get_class($reference);
            $attrs['ref_class_id'] = $reference->id;
        }

        try {
            $transaction = $this->create($attrs);
        } catch (ValidatorException $e) {
            throw $e;
        }

        # Adjust the balance on the journal
        $balance = new Money($journal->balance);
        if($credit) {
            $balance = $balance->add($credit);
        }

        if($debit) {
            $balance = $balance->subtract($debit);
        }

        $journal->balance = $balance->getAmount();
        $journal->save();

        return $transaction;
    }

    /**
     * @param Journal $journal
     * @param Carbon|null $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalance(Journal $journal, Carbon $date=null)
    {
        if(!$date) {
            $date = Carbon::now();
        }

        $credit = $this->getCreditBalanceOn($journal, $date);
        $debit = $this->getDebitBalanceOn($journal, $date);

        return $credit->subtract($debit);
    }

    /**
     * Get the credit only balance of the journal based on a given date.
     * @param Journal $journal
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getCreditBalanceOn(Journal $journal, Carbon $date)
    {
        $balance = $this->findWhere([
            'journal_id' => $journal->id,
            ['post_date', '<=', $date]
        ], ['id', 'credit'])->sum('credit') ?: 0;

        return new Money($balance);
    }

    /**
     * @param Journal $journal
     * @param Carbon $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getDebitBalanceOn(Journal $journal, Carbon $date): Money
    {
        $balance = $this->findWhere([
            'journal_id' => $journal->id,
            ['post_date', '<=', $date]
        ], ['id', 'debit'])->sum('debit') ?: 0;

        return new Money($balance);
    }
}
