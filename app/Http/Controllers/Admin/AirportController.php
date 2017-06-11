<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Requests\CreateAirportRequest;
use App\Http\Requests\UpdateAirportRequest;
use App\Repositories\AirportRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AirportController extends InfyOmBaseController
{
    /** @var  AirportRepository */
    private $airportRepository;

    public function __construct(AirportRepository $airportRepo)
    {
        $this->airportRepository = $airportRepo;
    }

    /**
     * Display a listing of the Airport.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->airportRepository->pushCriteria(new RequestCriteria($request));
        $airports = $this->airportRepository->all();

        return view('admin.airports.index')
            ->with('airports', $airports);
    }

    /**
     * Show the form for creating a new Airport.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.airports.create');
    }

    /**
     * Store a newly created Airport in storage.
     *
     * @param CreateAirportRequest $request
     *
     * @return Response
     */
    public function store(CreateAirportRequest $request)
    {
        $input = $request->all();

        $airport = $this->airportRepository->create($input);

        Flash::success('Airport saved successfully.');

        return redirect(route('admin.airports.index'));
    }

    /**
     * Display the specified Airport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');

            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.show')->with('airport', $airport);
    }

    /**
     * Show the form for editing the specified Airport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');

            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.edit')->with('airport', $airport);
    }

    /**
     * Update the specified Airport in storage.
     *
     * @param  int              $id
     * @param UpdateAirportRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAirportRequest $request)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');

            return redirect(route('admin.airports.index'));
        }

        $airport = $this->airportRepository->update($request->all(), $id);

        Flash::success('Airport updated successfully.');

        return redirect(route('admin.airports.index'));
    }

    /**
     * Remove the specified Airport from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');

            return redirect(route('admin.airports.index'));
        }

        $this->airportRepository->delete($id);

        Flash::success('Airport deleted successfully.');

        return redirect(route('admin.airports.index'));
    }
}
