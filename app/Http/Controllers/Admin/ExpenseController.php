<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Models\Enums\ExpenseType;
use App\Repositories\AirlineRepository;
use App\Repositories\ExpenseRepository;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ExpenseController extends BaseController
{
    private $airlineRepo,
            $expenseRepo;

    /**
     * expensesController constructor.
     * @param AirlineRepository $airlineRepo
     * @param ExpenseRepository $expenseRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        ExpenseRepository $expenseRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->expenseRepo = $expenseRepo;
    }

    /**
     * Display a listing of the expenses.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->expenseRepo->pushCriteria(new RequestCriteria($request));
        $expenses = $this->expenseRepo->all();

        return view('admin.expenses.index', [
            'expenses' => $expenses
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
        ]);
    }

    /**
     * Store a newly created expenses in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $this->expenseRepo->create($input);

        Flash::success('Expense saved successfully.');
        return redirect(route('admin.expenses.index'));
    }

    /**
     * Display the specified expenses.
     * @param  int $id
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
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $expense = $this->expenseRepo->findWithoutFail($id);

        if (empty($expense)) {
            Flash::error('Expense not found');
            return redirect(route('admin.expenses.index'));
        }

        return view('admin.expenses.edit', [
            'expense' => $expense,
            'airlines_list' => $this->airlineRepo->selectBoxList(true),
            'expense_types' => ExpenseType::select(),
        ]);
    }

    /**
     * Update the specified expenses in storage.
     * @param  int $id
     * @param Request $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @param  int $id
     * @return Response
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
}
