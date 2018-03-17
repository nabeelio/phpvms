<?php

namespace App\Listeners\Cron\Nightly;

use App\Events\CronNightly;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Repositories\JournalRepository;
use App\Support\Money;
use Log;

/**
 * This recalculates the balances on all of the journals
 * @package App\Listeners\Cron
 */
class RecalculateBalances
{
    private $journalRepo;

    /**
     * Nightly constructor.
     * @param JournalRepository $journalRepo
     */
    public function __construct(JournalRepository $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    /**
     * Recalculate all the balances for the different ledgers
     * @param CronNightly $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function handle(CronNightly $event): void
    {
        Log::info('Recalculating balances');

        $journals = Journal::all();
        foreach ($journals as $journal) {

            $where = ['journal_id' => $journal->id];
            $credits = Money::create(JournalTransaction::where($where)->sum('credit') ?: 0);
            $debits = Money::create(JournalTransaction::where($where)->sum('debit') ?: 0);
            $balance = $credits->subtract($debits);

            Log::info('Adjusting balance on ' .
                $journal->morphed_type . ':' . $journal->morphed_id
                . ' from ' . $journal->balance . ' to ' . $balance);

            $journal->balance = $balance->getAmount();
            $journal->save();
        }

        Log::info('Done calculating balances');
    }
}
