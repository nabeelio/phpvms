<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use App\Interfaces\Controller;
use App\Models\Aircraft;
use App\Models\Enums\AircraftStatus;
use App\Models\Expense;
use App\Models\Subfleet;
use App\Repositories\AircraftRepository;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AircraftController
 * @package App\Http\Controllers\Admin
 */
class AircraftController extends Controller
{
    private $aircraftRepo;

    /**
     * AircraftController constructor.
     * @param AircraftRepository $aircraftRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
    }

    /**
     * Display a listing of the Aircraft.
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->aircraftRepo->pushCriteria(new RequestCriteria($request));
        $aircraft = $this->aircraftRepo->orderBy('name', 'asc')->all();

        return view('admin.aircraft.index', [
            'aircraft' => $aircraft
        ]);
    }

    /**
     * Show the form for creating a new Aircraft.
     */
    public function create()
    {
        return view('admin.aircraft.create', [
            'subfleets' => Subfleet::all()->pluck('name', 'id'),
            'statuses'  => AircraftStatus::select(true),
        ]);
    }

    /**
     * Store a newly created Aircraft in storage.
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAircraftRequest $request)
    {
        $attrs = $request->all();
        $aircraft = $this->aircraftRepo->create($attrs);

        Flash::success('Aircraft saved successfully.');

        return redirect(route('admin.aircraft.edit', ['id' => $aircraft->id]));
    }

    /**
     * Display the specified Aircraft.
     */
    public function show($id)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');

            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.show', [
            'aircraft' => $aircraft,
        ]);
    }

    /**
     * Show the form for editing the specified Aircraft.
     */
    public function edit($id)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');

            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.edit', [
            'subfleets' => Subfleet::all()->pluck('name', 'id'),
            'statuses'  => AircraftStatus::select(true),
            'aircraft'  => $aircraft,
        ]);
    }

    /**
     * Update the specified Aircraft in storage.
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAircraftRequest $request)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');

            return redirect(route('admin.aircraft.index'));
        }

        $attrs = $request->all();
        $this->aircraftRepo->update($attrs, $id);

        Flash::success('Aircraft updated successfully.');

        return redirect(route('admin.aircraft.index'));
    }

    /**
     * Remove the specified Aircraft from storage.
     */
    public function destroy($id)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');

            return redirect(route('admin.aircraft.index'));
        }

        $this->aircraftRepo->delete($id);

        Flash::success('Aircraft deleted successfully.');

        return redirect(route('admin.aircraft.index'));
    }

    /**
     * @param Aircraft|null $aircraft
     * @return mixed
     */
    protected function return_expenses_view(?Aircraft $aircraft)
    {
        $aircraft->refresh();

        return view('admin.aircraft.expenses', [
            'aircraft' => $aircraft,
        ]);
    }

    /**
     * Operations for associating ranks to the subfleet
     * @param         $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function expenses($id, Request $request)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);
        if (empty($aircraft)) {
            return $this->return_expenses_view($aircraft);
        }

        if ($request->isMethod('get')) {
            return $this->return_expenses_view($aircraft);
        }

        if ($request->isMethod('post')) {
            $expense = new Expense($request->post());
            $expense->ref_class = Aircraft::class;
            $expense->ref_class_id = $aircraft->id;
            $expense->save();
        } elseif ($request->isMethod('put')) {
            $expense = Expense::findOrFail($request->input('expense_id'));
            $expense->{$request->name} = $request->value;
            $expense->save();
        } // dissassociate fare from teh aircraft
        elseif ($request->isMethod('delete')) {
            $expense = Expense::findOrFail($request->input('expense_id'));
            $expense->delete();
        }

        return $this->return_expenses_view($aircraft);
    }
}
