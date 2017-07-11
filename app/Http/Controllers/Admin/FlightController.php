<?php

namespace App\Http\Controllers\Admin;

use App\Models\Airline;
use App\Models\FlightFields;
use App\Models\Airport;
use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FlightController extends BaseController
{
    /** @var  FlightRepository */
    private $flightRepository, $subfleetRepo;

    public function __construct(
        FlightRepository $flightRepo,
        SubfleetRepository $subfleetRepo
        )
    {
        $this->flightRepository = $flightRepo;
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
     * Display a listing of the Flight.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->flightRepository->pushCriteria(new RequestCriteria($request));
        $flights = $this->flightRepository->all();
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
        return view('admin.flights.create');
    }

    /**
     * Store a newly created Flight in storage.
     *
     * @param CreateFlightRequest $request
     *
     * @return Response
     */
    public function store(CreateFlightRequest $request)
    {
        $input = $request->all();

        $flight = $this->flightRepository->create($input);

        Flash::success('Flight saved successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * Display the specified Flight.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $flight = $this->flightRepository->findWithoutFail($id);

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
     * Show the form for editing the specified Flight.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $flight = $this->flightRepository->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.edit', [
            'flight' => $flight,
            'airlines' => Airline::all()->pluck('name', 'id'),
            'airports' => Airport::all()->pluck('icao', 'id'),
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * Update the specified Flight in storage.
     *
     * @param  int              $id
     * @param UpdateFlightRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFlightRequest $request)
    {
        $flight = $this->flightRepository->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $flight = $this->flightRepository->update($request->all(), $id);

        Flash::success('Flight updated successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * Remove the specified Flight from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $flight = $this->flightRepository->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $this->flightRepository->delete($id);

        Flash::success('Flight deleted successfully.');
        return redirect(route('admin.flights.index'));
    }

    protected function return_fields_view($flight)
    {
        $flight->refresh();
        return view('admin.flights.flight_fields', [
            'flight' => $flight,
        ]);
    }

    public function fields(Request $request)
    {
        print_r($request->toArray());
        $id = $request->id;

        $flight = $this->flightRepository->findWithoutFail($id);
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

    protected function return_subfleet_view($flight)
    {
        $avail_subfleets = $this->getAvailSubfleets($flight);
        return view('admin.flights.subfleets', [
            'flight' => $flight,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    public function subfleets(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepository->findWithoutFail($id);
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
