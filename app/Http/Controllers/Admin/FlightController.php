<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Repositories\FlightRepository;
use App\Repositories\AircraftRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FlightController extends BaseController
{
    /** @var  FlightRepository */
    private $flightRepository, $aircraftRepository;

    public function __construct(
        FlightRepository $flightRepo,
        AircraftRepository $aircraftRepository
        )
    {
        $this->flightRepository = $flightRepo;
        $this->aircraftRepository = $aircraftRepository;
    }

    protected function getAvailAircraft($flight)
    {
        $retval = [];

        $flight->refresh();
        $all_aircraft = $this->aircraftRepository->all();
        $avail_aircraft = $all_aircraft->except($flight->aircraft->modelKeys());

        foreach ($avail_aircraft as $ac) {
            $retval[$ac->id] = $ac->icao.' - '.$ac->registration;
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

        return view('admin.flights.index')
                ->with('flights', $flights);
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

        $avail_aircraft = $this->getAvailAircraft($flight);
        return view('admin.flights.show')
                ->with('flight', $flight)
                ->with('avail_aircraft', $avail_aircraft);
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

        return view('admin.flights.edit')->with('flight', $flight);
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

    protected function return_aircraft_view($flight)
    {
        $avail_aircraft = $this->getAvailAircraft($flight);
        return view('admin.flights.aircraft')
            ->with('flight', $flight)
            ->with('avail_aircraft', $avail_aircraft);
    }

    public function aircraft(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepository->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add aircraft to flight
        if ($request->isMethod('post')) {
            $flight->aircraft()->syncWithoutDetaching([$request->aircraft_id]);
        }

        // remove aircraft from flight
        elseif ($request->isMethod('delete')) {
            $flight->aircraft()->detach($request->aircraft_id);
        }

        return $this->return_aircraft_view($flight);
    }
}
