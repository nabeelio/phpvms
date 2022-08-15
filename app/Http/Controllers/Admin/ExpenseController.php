<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Controllers\Admin\Traits\Importable;
use App\Models\Enums\ExpenseType;
use App\Models\Enums\FlightType;
use App\Models\Enums\ImportExportType;
use App\Models\Expense;
use App\Repositories\AirlineRepository;
use App\Repositories\ExpenseRepository;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class ExpenseController extends Controller
{
    use Importable;

    private AirlineRepository $airlineRepo;
    private ExpenseRepository $expenseRepo;
    private ImportService $importSvc;

    /**
     * expensesController constructor.
     *
     * @param AirlineRepository $airlineRepo
     * @param ExpenseRepository $expenseRepo
     * @param ImportService     $importSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        ExpenseRepository $expenseRepo,
        ImportService $importSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->expenseRepo = $expenseRepo;
        $this->importSvc = $importSvc;
    }

    /**
     * Display a listing of the expenses.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->expenseRepo->pushCriteria(new RequestCriteria($request));
        $expenses = $this->expenseRepo->findWhere([
            'ref_model' => Expense::class,
        ]);

        return view('admin.expenses.index', [
            'expenses' => $expenses,
        ]);
    }

    /**
     * Show the form for creating a new expenses.
     */
    public function create()
    {
        return view('admin.expenses.create', [
            'airlines_list' => $this->airlineRepo->selectBoxList(true),
            'expense_types' => ExpenseType::select(),
            'flight_types'  => FlightType::select(),
        ]);
    }

    /**
     * Store a newly created expenses in storage.
     *
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['ref_model'] = Expense::class;
        $this->expenseRepo->create($input);

        Flash::success('Expense saved successfully.');

        return redirect(route('admin.expenses.index'));
    }

    /**
     * Display the specified expenses.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $expenses = $this->expenseRepo->findWithoutFail($id);

        if (empty($expenses)) {
            Flash::error('expenses not found');

            return redirect(route('admin.expenses.index'));
        }

        return view('admin.expenses.show', [
            'expenses' => $expenses,
        ]);
    }

    /**
     * Show the form for editing the specified expenses.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $expense = $this->expenseRepo->findWithoutFail($id);

        if (empty($expense)) {
            Flash::error('Expense not found');

            return redirect(route('admin.expenses.index'));
        }

        return view('admin.expenses.edit', [
            'expense'       => $expense,
            'airlines_list' => $this->airlineRepo->selectBoxList(true),
            'expense_types' => ExpenseType::select(),
            'flight_types'  => FlightType::select(),
        ]);
    }

    /**
     * Update the specified expenses in storage.
     *
     * @param int     $id
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $expenses = $this->expenseRepo->findWithoutFail($id);

        if (empty($expenses)) {
            Flash::error('Expense not found');
            return redirect(route('admin.expenses.index'));
        }

        $this->expenseRepo->update($request->all(), $id);

        Flash::success('Expense updated successfully.');
        return redirect(route('admin.expenses.index'));
    }

    /**
     * Remove the specified expenses from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $expenses = $this->expenseRepo->findWithoutFail($id);

        if (empty($expenses)) {
            Flash::error('Expense not found');
            return redirect(route('admin.expenses.index'));
        }

        $this->expenseRepo->delete($id);

        Flash::success('Expense deleted successfully.');
        return redirect(route('admin.expenses.index'));
    }

    /**
     * Run the airport exporter
     *
     * @param Request $request
     *
     * @throws \League\Csv\Exception
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $exporter = app(ExportService::class);
        $expenses = $this->expenseRepo->all();

        $path = $exporter->exportExpenses($expenses);
        return response()
            ->download($path, 'expenses.csv', [
                'content-type' => 'text/csv',
            ])
            ->deleteFileAfterSend(true);
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $logs = [
            'success' => [],
            'errors'  => [],
        ];

        if ($request->isMethod('post')) {
            $logs = $this->importFile($request, ImportExportType::EXPENSES);
        }

        return view('admin.expenses.import', [
            'logs' => $logs,
        ]);
    }
}
