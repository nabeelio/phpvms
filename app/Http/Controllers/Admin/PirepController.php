<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Repositories\AircraftRepository;
use App\Repositories\PirepRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PirepController extends BaseController
{
    private $pirepRepo, $aircraftRepo;

    public function __construct(PirepRepository $pirepRepo, AircraftRepository $aircraftRepo)
    {
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function aircraftList()
    {
        $retval = [];
        $all_aircraft = $this->aircraftRepo->all();

        foreach ($all_aircraft as $ac) {
            $retval[$ac->id] = $ac->subfleet->name.' - '.$ac->name.' ('.$ac->registration.')';
        }

        return $retval;
    }

    /**
     * Display a listing of the Pirep.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $criterea = new RequestCriteria($request);
        $this->pirepRepo->pushCriteria($criterea);

        $pireps = $this->pirepRepo
                       ->orderBy('created_at', 'desc')
                       ->all();

        return view('admin.pireps.index', [
            'pireps' => $pireps
        ]);
    }

    /**
     * Show the form for creating a new Pirep.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.pireps.create');
    }

    /**
     * Store a newly created Pirep in storage.
     *
     * @param CreatePirepRequest $request
     *
     * @return Response
     */
    public function store(CreatePirepRequest $request)
    {
        $input = $request->all();
        $pirep = $this->pirepRepo->create($input);

        Flash::success('Pirep saved successfully.');
        return redirect(route('admin.pireps.index'));
    }

    /**
     * Display the specified Pirep.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $pirep = $this->pirepRepo->find($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        return view('admin.pireps.show', [
            'pirep' => $pirep,
        ]);
    }

    /**
     * Show the form for editing the specified Pirep.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        return view('admin.pireps.edit', [
            'pirep' => $pirep,
            'aircraft' => $this->aircraftList(),
        ]);
    }

    /**
     * Update the specified Pirep in storage.
     *
     * @param  int              $id
     * @param UpdatePirepRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePirepRequest $request)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $pirep = $this->pirepRepo->update($request->all(), $id);

        Flash::success('Pirep updated successfully.');
        return redirect(route('admin.pireps.index'));
    }

    /**
     * Remove the specified Pirep from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $this->pirepRepo->delete($id);

        Flash::success('Pirep deleted successfully.');
        return redirect(route('admin.pireps.index'));
    }
}
