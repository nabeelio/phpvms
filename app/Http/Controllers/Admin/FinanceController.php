<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enums\JournalType;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Repositories\AirlineRepository;
use App\Repositories\JournalRepository;
use App\Services\Finance\PirepFinanceService;
use App\Support\Dates;
use App\Support\Money;
use Illuminate\Http\Request;

/**
 * Class FinanceController
 * @package App\Http\Controllers\Admin
 */
class FinanceController extends BaseController
{
    private $airlineRepo,
            $financeSvc,
            $journalRepo;

    /**
     * @param AirlineRepository $airlineRepo
     * @param PirepFinanceService $financeSvc
     * @param JournalRepository $journalRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        PirepFinanceService $financeSvc,
        JournalRepository $journalRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->financeSvc = $financeSvc;
        $this->journalRepo = $journalRepo;
    }

    /**
     * Display the summation tables for a given month by airline
     * @param Request $request
     * @return mixed
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $between = Dates::getMonthBoundary($month);

        $first_journal = Journal::where(['type' => JournalType::AIRLINE])
            ->orderBy('created_at', 'asc')
            ->limit(1)
            ->first();

        $transaction_groups = [];
        $airlines = $this->airlineRepo->orderBy('icao')->all();

        # group by the airline
        foreach($airlines as $airline) {

            # Return all the transactions, grouped by the transaction group
            $transactions = JournalTransaction::groupBy('transaction_group')
                ->selectRaw('transaction_group, currency, 
                             SUM(credit) as sum_credits, 
                             SUM(debit) as sum_debits')
                ->where([
                    'journal_id' => $airline->journal->id
                ])
                ->whereBetween('created_at', $between, 'AND')
                ->orderBy('sum_credits', 'desc')
                ->orderBy('sum_debits', 'desc')
                ->orderBy('transaction_group', 'asc')
                ->get();

            # Summate it so we can show it on the footer of the table
            $sum_all_credits = 0;
            $sum_all_debits = 0;
            foreach ($transactions as $ta) {
                $sum_all_credits += $ta->sum_credits ?? 0;
                $sum_all_debits += $ta->sum_debits ?? 0;
            }

            $transaction_groups[] = [
                'airline'       => $airline,
                'credits'       => new Money($sum_all_credits),
                'debits'        => new Money($sum_all_debits),
                'transactions'  => $transactions,
            ];
        }

        return view('admin.finances.index', [
            'current_month' => $month,
            'months_list' => Dates::getMonthsList($first_journal->created_at),
            'transaction_groups' => $transaction_groups,
        ]);
    }

    /**
     * Show a month
     */
    public function show($id)
    {

    }
}
