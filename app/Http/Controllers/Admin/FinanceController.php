<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Enums\JournalType;
use App\Models\Journal;
use App\Repositories\AirlineRepository;
use App\Services\FinanceService;
use App\Support\Dates;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    private AirlineRepository $airlineRepo;
    private FinanceService $financeSvc;

    /**
     * @param AirlineRepository $airlineRepo
     * @param FinanceService    $financeSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        FinanceService $financeSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->financeSvc = $financeSvc;
    }

    /**
     * Display the summation tables for a given month by airline
     *
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $transaction_groups = $this->financeSvc->getAllAirlineTransactionsBetween($month);

        $first_journal = Journal::select(['created_at'])
            ->where(['type' => JournalType::AIRLINE])
            ->orderBy('created_at', 'asc')
            ->limit(1)
            ->first();

        return view('admin.finances.index', [
            'current_month'      => $month,
            'months_list'        => Dates::getMonthsList($first_journal->created_at),
            'transaction_groups' => $transaction_groups,
        ]);
    }

    /**
     * Show a month
     *
     * @param mixed $id
     */
    public function show($id)
    {
    }
}
