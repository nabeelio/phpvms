<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subfleet;
use App\Http\Requests\CreateAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use App\Repositories\AircraftRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;


class AircraftController extends BaseController
{
    private $aircraftRepository;

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
        $aircraft = $this->aircraftRepository->all();

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
        ]);
    }

    /**
     * Store a newly created Aircraft in storage.
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAircraftRequest $request)
    {
        $input = $request->all();
        $this->aircraftRepository->create($input);

        Flash::success('Aircraft saved successfully.');
        return redirect(route('admin.aircraft.index'));
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

        $this->aircraftRepository->update($request->all(), $id);

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
