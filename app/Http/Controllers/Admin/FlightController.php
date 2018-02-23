<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Models\Enums\FlightType;
use App\Models\Flight;
use App\Models\FlightFields;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\FareRepository;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use App\Services\FareService;
use App\Services\FlightService;
use App\Support\Units\Time;
use Flash;
use Illuminate\Http\Request;
use Response;

class FlightController extends BaseController
{
    private $airlineRepo,
            $airportRepo,
            $fareRepo,
            $flightRepo,
            $fareSvc,
            $flightSvc,
            $subfleetRepo;

    /**
     * FlightController constructor.
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param FareRepository $fareRepo
     * @param FlightRepository $flightRepo
     * @param FareService $fareSvc
     * @param FlightService $flightSvc
     * @param SubfleetRepository $subfleetRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FareRepository $fareRepo,
        FlightRepository $flightRepo,
        FareService $fareSvc,
        FlightService $flightSvc,
        SubfleetRepository $subfleetRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->fareRepo = $fareRepo;
        $this->flightRepo = $flightRepo;
        $this->fareSvc = $fareSvc;
        $this->flightSvc = $flightSvc;
        $this->subfleetRepo = $subfleetRepo;
    }

    /**
     * @param $flight
     * @return array
     */
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
        $flights = $this->flightRepo->searchCriteria($request, false)->paginate();
        return view('admin.flights.index', [
            'flights' => $flights,
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
        ]);
    }

    /**
     * Show the form for creating a new Flight.
     * @return Response
     */
    public function create()
    {
        return view('admin.flights.create', [
            'flight'   => null,
            'airlines' => $this->airlineRepo->selectBoxList(),
            'airports' => $this->airportRepo->selectBoxList(true, false),
            'flight_types' => FlightType::select(true),
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

        # See if flight number exists with the route code/leg
        $where = [
            'flight_number' => $input['flight_number'],
        ];

        if(filled($input['route_code'])) {
            $where['route_code'] = $input['route_code'];
        }

        if(filled($input['route_leg'])) {
            $where['route_leg'] = $input['route_leg'];
        }

        $flights = $this->flightRepo->findWhere($where);
        if($flights->count() > 0) {
            Flash::error('Duplicate flight with same number/code/leg found, please change to proceed');
            return redirect()->back()->withInput($request->all());
        }

        $input['active'] = get_truth_state($input['active']);

        $time = new Time($input['minutes'], $input['hours']);
        $input['flight_time'] = $time->getMinutes();

        $flight = $this->flightRepo->create($input);

        Flash::success('Flight saved successfully.');
        return redirect(route('admin.flights.edit', $flight->id));
    }

    /**
     * @param $id
     * @return mixed
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
     * @return mixed
     */
    public function edit($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $time = new Time($flight->flight_time);
        $flight->hours = $time->hours;
        $flight->minutes = $time->minutes;

        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.edit', [
            'flight' => $flight,
            'airlines' => $this->airlineRepo->selectBoxList(),
            'airports' => $this->airportRepo->selectBoxList(),
            'avail_fares' => $this->getAvailFares($flight),
            'avail_subfleets' => $avail_subfleets,
            'flight_types' => FlightType::select(true),
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

        $input = $request->all();

        # See if flight number exists with the route code/leg
        $where = [
            ['id', '<>', $id],
            'flight_number' => $input['flight_number'],
        ];

        if (filled($input['route_code'])) {
            $where['route_code'] = $input['route_code'];
        }

        if (filled($input['route_leg'])) {
            $where['route_leg'] = $input['route_leg'];
        }

        $flights = $this->flightRepo->findWhere($where);
        if ($flights->count() > 0) {
            Flash::error('Duplicate flight with same number/code/leg found, please change to proceed');
            return redirect()->back()->withInput($request->all());
        }

        $input['flight_time'] = Time::init(
            $input['minutes'],
            $input['hours'])->getMinutes();

        $input['active'] = get_truth_state($input['active']);

        $this->flightRepo->update($input, $id);

        Flash::success('Flight updated successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $this->flightSvc->deleteFlight($flight);

        Flash::success('Flight deleted successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * @param $flight
     * @return mixed
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
     * @return mixed
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
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function subfleets($id, Request $request)
    {
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

    /**
     * Get all the fares that haven't been assigned to a given subfleet
     */
    protected function getAvailFares($flight)
    {
        $retval = [];
        $all_fares = $this->fareRepo->all();
        $avail_fares = $all_fares->except($flight->fares->modelKeys());
        foreach ($avail_fares as $fare) {
            $retval[$fare->id] = $fare->name .
                ' (base price: '.$fare->price.')';
        }

        return $retval;
    }

    /**
     * @param Flight $flight
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function return_fares_view(Flight $flight)
    {
        $flight->refresh();
        return view('admin.flights.fares', [
            'flight' => $flight,
            'avail_fares' => $this->getAvailFares($flight),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fares(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            return $this->return_fares_view($flight);
        }

        if ($request->isMethod('get')) {
            return $this->return_fares_view($flight);
        }

        if ($request->isMethod('delete')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $this->fareSvc->delFareFromFlight($flight, $fare);
            return $this->return_fares_view($flight);
        }

        $this->validate($request, [
            'value' => 'nullable',  // regex:/([\d%]*)/
        ]);

        /**
         * update specific fare data
         */
        if ($request->isMethod('post')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $this->fareSvc->setForFlight($flight, $fare);
        } // update the pivot table with overrides for the fares
        elseif ($request->isMethod('put')) {
            $override = [];
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $override[$request->name] = $request->value;
            $this->fareSvc->setForFlight($flight, $fare, $override);
        } // dissassociate fare from teh aircraft

        return $this->return_fares_view($flight);
    }
}
