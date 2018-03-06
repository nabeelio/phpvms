<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAirportRequest;
use App\Http\Requests\UpdateAirportRequest;
use App\Models\Airport;
use App\Models\Expense;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use Flash;
use Illuminate\Http\Request;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Response;


class AirportController extends BaseController
{
    /** @var  AirportRepository */
    private $airportRepo;

    /**
     * AirportController constructor.
     * @param AirportRepository $airportRepo
     */
    public function __construct(
        AirportRepository $airportRepo
    ) {
        $this->airportRepo = $airportRepo;
    }

    /**
     * Display a listing of the Airport.
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->has('icao')) {
            $where['icao'] = $request->get('icao');
        }

        $this->airportRepo->pushCriteria(new WhereCriteria($request, $where));
        $airports = $this->airportRepo
                         ->orderBy('icao', 'asc')
                         ->paginate();

        return view('admin.airports.index', [
            'airports' => $airports,
        ]);
    }

    /**
     * Show the form for creating a new Airport.
     * @return Response
     */
    public function create()
    {
        return view('admin.airports.create', [
            'timezones' => Timezonelist::toArray(),
        ]);
    }

    /**
     * Store a newly created Airport in storage.
     * @param CreateAirportRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAirportRequest $request)
    {
        $input = $request->all();
        $input['hub'] = get_truth_state($input['hub']);

        $this->airportRepo->create($input);

        Flash::success('Airport saved successfully.');
        return redirect(route('admin.airports.index'));
    }

    /**
     * Display the specified Airport.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $airport = $this->airportRepo->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.show', [
            'airport' => $airport,
        ]);
    }

    /**
     * Show the form for editing the specified Airport.
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $airport = $this->airportRepo->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.edit', [
            'timezones' => Timezonelist::toArray(),
            'airport' => $airport,
        ]);
    }

    /**
     * Update the specified Airport in storage.
     * @param  int $id
     * @param UpdateAirportRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAirportRequest $request)
    {
        $airport = $this->airportRepo->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        $attrs = $request->all();
        $attrs['hub'] = get_truth_state($attrs['hub']);

        $this->airportRepo->update($attrs, $id);

        Flash::success('Airport updated successfully.');
        return redirect(route('admin.airports.index'));
    }

    /**
     * Remove the specified Airport from storage.
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $airport = $this->airportRepo->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        $this->airportRepo->delete($id);

        Flash::success('Airport deleted successfully.');
        return redirect(route('admin.airports.index'));
    }

    /**
     * @param Airport|null $airport
     * @return mixed
     */
    protected function return_expenses_view(?Airport $airport)
    {
        $airport->refresh();
        return view('admin.airports.expenses', [
            'airport' => $airport,
        ]);
    }

    /**
     * Operations for associating ranks to the subfleet
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function expenses($id, Request $request)
    {
        $airport = $this->airportRepo->findWithoutFail($id);
        if (empty($airport)) {
            return $this->return_expenses_view($airport);
        }

        if ($request->isMethod('get')) {
            return $this->return_expenses_view($airport);
        }

        if ($request->isMethod('post')) {
            $expense = new Expense($request->post());
            $expense->ref_class = Airport::class;
            $expense->ref_class_id = $airport->id;
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

        return $this->return_expenses_view($airport);
    }

    /**
     * Set fuel prices for this airport
     * @param Request $request
     * @return mixed
     */
    public function fuel(Request $request)
    {
        $id = $request->id;

        $airport = $this->airportRepo->findWithoutFail($id);
        if (empty($airport)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add aircraft to flight
        if ($request->isMethod('put')) {
            $airport->{$request->name} = $request->value;
        }

        $airport->save();
    }
}
