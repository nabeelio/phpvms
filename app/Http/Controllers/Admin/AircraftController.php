<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use App\Models\Enums\AircraftStatus;
use App\Models\Subfleet;
use App\Repositories\AircraftRepository;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;


class AircraftController extends BaseController
{
    private $aircraftRepository;

    /**
     * AircraftController constructor.
     * @param AircraftRepository $aircraftRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo
    ) {
        $this->aircraftRepository = $aircraftRepo;
    }

    /**
     * Display a listing of the Aircraft.
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->aircraftRepository->pushCriteria(new RequestCriteria($request));
        $aircraft = $this->aircraftRepository->orderBy('name', 'asc')->all();

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
            'statuses' => AircraftStatus::select(true),
        ]);
    }

    /**
     * Store a newly created Aircraft in storage.
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAircraftRequest $request)
    {
        $attrs = $request->all();
        $aircraft = $this->aircraftRepository->create($attrs);

        Flash::success('Aircraft saved successfully.');
        return redirect(route('admin.aircraft.edit', ['id' => $aircraft->id]));
    }

    /**
     * Display the specified Aircraft.
     */
    public function show($id)
    {
        $aircraft = $this->aircraftRepository->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.show', [
            'aircraft'    => $aircraft,
        ]);
    }

    /**
     * Show the form for editing the specified Aircraft.
     */
    public function edit($id)
    {
        $aircraft = $this->aircraftRepository->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.edit', [
            'subfleets' => Subfleet::all()->pluck('name', 'id'),
            'statuses' => AircraftStatus::select(true),
            'aircraft'  => $aircraft,
        ]);
    }

    /**
     * Update the specified Aircraft in storage.
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAircraftRequest $request)
    {
        $aircraft = $this->aircraftRepository->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        $attrs = $request->all();
        $this->aircraftRepository->update($attrs, $id);

        Flash::success('Aircraft updated successfully.');
        return redirect(route('admin.aircraft.index'));
    }

    /**
     * Remove the specified Aircraft from storage.
     */
    public function destroy($id)
    {
        $aircraft = $this->aircraftRepository->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        $this->aircraftRepository->delete($id);

        Flash::success('Aircraft deleted successfully.');
        return redirect(route('admin.aircraft.index'));
    }
}
