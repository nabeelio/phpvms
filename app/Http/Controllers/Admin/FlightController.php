<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Repositories\FlightRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FlightController extends BaseController
{
    /** @var  FlightRepository */
    private $flightRepository;

    public function __construct(FlightRepository $flightRepo)
    {
        $this->flightRepository = $flightRepo;
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

        return view('admin.flights.show')->with('flight', $flight);
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

    public function aircraft(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepository->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        /**
         * update specific aircraftdata
         */
        if ($request->isMethod('post')) {
            // add
        }

        // update the pivot table with overrides for the fares
        elseif ($request->isMethod('put')) {
            // update
        }

        // dissassociate fare from teh aircraft
        elseif ($request->isMethod('delete')) {
            // del
        }
    }
}
