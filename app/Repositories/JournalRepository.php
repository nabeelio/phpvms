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

/**
 * Class JournalRepository
 * @package App\Repositories
 */
class JournalRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * @return string
     */
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
        Journal &$journal,
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
            'currency'          => config('phpvms.currency'),
            'memo'              => $memo,
            'post_date'         => $post_date ?? Carbon::now(),
        ];

        if($reference !== null) {
            $attrs['ref_class'] = \get_class($reference);
            $attrs['ref_class_id'] = $reference->id;
        }

        if($transaction_group) {
            $transaction_group = str_replace(' ', '_', $transaction_group);
            $attrs['transaction_group'] = $transaction_group;
        }

        try {
            $transaction = $this->create($attrs);
        } catch (ValidatorException $e) {
            throw $e;
        }

        $journal->refresh();

        return $transaction;
    }

    /**
     * @param Journal $journal
     * @param Carbon|null $date
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getBalance(Journal $journal=null, Carbon $date=null)
    {
        if(!$date) {
            $date = Carbon::now();
        }

        $credit = $this->getCreditBalanceBetween($date, $journal);
        $debit = $this->getDebitBalanceBetween($date, $journal);

        return $credit->subtract($debit);
    }

    /**
     * Get the credit only balance of the journal based on a given date.
     * @param Carbon $date
     * @param Journal $journal
     * @param Carbon|null $start_date
     * @param null $transaction_group
     * @return Money
     */
    public function getCreditBalanceBetween(
        Carbon $date,
        Journal $journal=null,
        Carbon $start_date=null,
        $transaction_group=null
    ): Money {

        $where = [
            ['post_date', '<=', $date]
        ];

        if($journal) {
            $where['journal_id'] = $journal->id;
        }

        if ($start_date) {
            $where[] = ['post_date', '>=', $start_date];
        }

        if ($transaction_group) {
            $where['transaction_group'] = $transaction_group;
        }

        $balance = $this
            ->findWhere($where, ['id', 'credit'])
            ->sum('credit') ?: 0;

        return new Money($balance);
    }

    /**
     * @param Carbon $date
     * @param Journal $journal
     * @param Carbon|null $start_date
     * @param null $transaction_group
     * @return Money
     */
    public function getDebitBalanceBetween(
        Carbon $date,
        Journal $journal=null,
        Carbon $start_date=null,
        $transaction_group=null
    ): Money {

        $where = [
            ['post_date', '<=', $date]
        ];

        if ($journal) {
            $where['journal_id'] = $journal->id;
        }

        if($start_date) {
            $where[] = ['post_date', '>=', $start_date];
        }

        if($transaction_group) {
            $where['transaction_group'] = $transaction_group;
        }

        $balance = $this
            ->findWhere($where, ['id', 'debit'])
            ->sum('debit') ?: 0;

        return new Money($balance);
    }

    /**
     * Return all transactions for a given object
     * @param $object
     * @param null $journal
     * @return array
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getAllForObject($object, $journal=null)
    {
        $where = [
            'ref_class' => \get_class($object),
            'ref_class_id' => $object->id,
        ];

        if($journal) {
            $where['journal_id'] = $journal->id;
        }

        $transactions = $this->whereOrder($where, [
                'credit' => 'desc',
                'debit' => 'desc'
            ])->get();

        return [
            'credits' => new Money($transactions->sum('credit')),
            'debits' => new Money($transactions->sum('debit')),
            'transactions' => $transactions,
        ];
    }

    /**
     * Delete all transactions for a given object
     * @param $object
     * @param null $journal
     * @return void
     */
    public function deleteAllForObject($object, $journal = null)
    {
        $where = [
            'ref_class' => \get_class($object),
            'ref_class_id' => $object->id,
        ];

        if ($journal) {
            $where['journal_id'] = $journal->id;
        }

        $this->deleteWhere($where);
    }
}
