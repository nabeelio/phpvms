<?php

namespace App\Http\Controllers\Admin;

use App\Models\Airline;
use App\Models\FlightFields;
use App\Models\Airport;
use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FlightController extends BaseController
{
    private $airlineRepo,
            $airportRepo,
            $flightRepo,
            $subfleetRepo;

    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FlightRepository $flightRepo,
        SubfleetRepository $subfleetRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    protected function getAvailSubfleets($flight)
    {
        $retval = [];

        $flight->refresh();
        $all_aircraft = $this->subfleetRepo->all();
        $avail_fleets = $all_aircraft->except($flight->subfleets->modelKeys());

        foreach ($avail_fleets as $ac) {
            $retval[$ac->id] = $ac->type.' - '.$ac->name;
        }

        return $retval;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->flightRepo->pushCriteria(new RequestCriteria($request));
        $flights = $this->flightRepo->paginate(10);
        return view('admin.flights.index', [
            'flights' => $flights,
        ]);
    }

    /**
     * Show the form for creating a new Flight.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.flights.create', [
            'flight'   => null,
            'airlines' => $this->airlineRepo->selectBoxList(),
            'airports' => $this->airportRepo->selectBoxList(),
        ]);
    }

    /**
     * @param CreateFlightRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateFlightRequest $request)
    {
        $input = $request->all();

        $flight = $this->flightRepo->create($input);

        Flash::success('Flight saved successfully.');
        return redirect(route('admin.flights.edit', $flight->id));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.show', [
            'flight' => $flight,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.edit', [
            'flight' => $flight,
            'airlines' => $this->airlineRepo->selectBoxList(),
            'airports' => $this->airportRepo->selectBoxList(),
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param $id
     * @param UpdateFlightRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateFlightRequest $request)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $flight = $this->flightRepo->update($request->all(), $id);

        Flash::success('Flight updated successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $this->flightRepo->delete($id);

        Flash::success('Flight deleted successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * @param $flight
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function return_fields_view($flight)
    {
        $flight->refresh();
        return view('admin.flights.flight_fields', [
            'flight' => $flight,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function fields(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add custom field to flight
        if ($request->isMethod('post')) {
            $field = new FlightFields;
            $field->flight_id = $id;
            $field->name = $request->name;
            $field->value = $request->value;
            $field->save();
        }

        elseif ($request->isMethod('put')) {
            $field = FlightFields::where('id', $request->field_id)->first();
            $field->value = $request->value;
            $field->save();
            // update the field value
        }

        // remove custom field from flight
        elseif ($request->isMethod('delete')) {
            FlightFields::destroy($request->field_id);
        }

        return $this->return_fields_view($flight);
    }

    /**
     * @param $flight
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function return_subfleet_view($flight)
    {
        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.subfleets', [
            'flight' => $flight,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function subfleets(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add aircraft to flight
        if ($request->isMethod('post')) {
            $flight->subfleets()->syncWithoutDetaching([$request->subfleet_id]);
        }

        // remove aircraft from flight
        elseif ($request->isMethod('delete')) {
            $flight->subfleets()->detach($request->subfleet_id);
        }

        return $this->return_subfleet_view($flight);
    }
}
