<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\JournalRepository;
use App\Services\FinanceService;
use Illuminate\Http\Request;

/**
 * Class FinanceController
 * @package App\Http\Controllers\Admin
 */
class FinanceController extends BaseController
{
    private $financeSvc,
            $journalRepo;

    /**
     * @param FinanceService $financeSvc
     * @param JournalRepository $journalRepo
     */
    public function __construct(
        FinanceService $financeSvc,
        JournalRepository $journalRepo
    ) {
        $this->financeSvc = $financeSvc;
        $this->journalRepo = $journalRepo;
    }

    /**
     * Display a listing of the Aircraft.
     */
    public function index(Request $request)
    {

    }

    /**
     * Show a month
     */
    public function show($id)
    {

    }
}
