<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subfleet;
use App\Http\Requests\CreateAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use App\Repositories\SubfleetRepository;
use App\Repositories\FareRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AircraftController extends BaseController
{
    /** @var  SubfleetRepository */
    private $aircraftRepository, $fareRepository;

    public function __construct(
        SubfleetRepository $aircraftRepo,
        FareRepository $fareRepo
    ) {
        $this->fareRepository = $fareRepo;
        $this->aircraftRepository = $aircraftRepo;
    }

    /**
     * Display a listing of the Aircraft.
     */
    public function index(Request $request)
    {
        $this->aircraftRepository->pushCriteria(new RequestCriteria($request));
        $aircraft = $this->aircraftRepository->all();

        return view('admin.aircraft.index')
            ->with('aircraft', $aircraft);
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
     */
    public function store(CreateAircraftRequest $request)
    {
        $input = $request->all();
        $aircraft = $this->aircraftRepository->create($input);

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
     */
    public function update($id, UpdateAircraftRequest $request)
    {
        $aircraft = $this->aircraftRepository->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        $aircraft = $this->aircraftRepository->update($request->all(), $id);

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
