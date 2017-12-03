<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Repositories\AircraftRepository;
use App\Repositories\PirepRepository;
use App\Services\PIREPService;
use Illuminate\Http\Request;
use Flash;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PirepController extends BaseController
{
    private $pirepRepo, $aircraftRepo, $pirepSvc;

    public function __construct(
        AircraftRepository $aircraftRepo,
        PirepRepository $pirepRepo,
        PIREPService $pirepSvc
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
        $this->pirepSvc = $pirepSvc;
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $criterea = new RequestCriteria($request);
        $this->pirepRepo->pushCriteria($criterea);

        $pireps = $this->pirepRepo
                       ->orderBy('created_at', 'desc')
                       ->paginate();

        return view('admin.pireps.index', [
            'pireps' => $pireps
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function pending(Request $request)
    {
        $criterea = new RequestCriteria($request);
        $this->pirepRepo->pushCriteria($criterea);

        $pireps = $this->pirepRepo
            ->findWhere(['status' => config('enums.pirep_status.PENDING')])
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('admin.pireps.index', [
            'pireps' => $pireps
        ]);
    }

    /**
     * Show the form for creating a new Pirep.
     * @return Response
     */
    public function create()
    {
        return view('admin.pireps.create');
    }

    /**
     * @param CreatePirepRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @param  int $id
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
     * @param  int $id
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
     * @param $id
     * @param UpdatePirepRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @param  int $id
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

    /**
     * Change or update the PIREP status
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function status(Request $request)
    {
        Log::info('PIREP status update call', [$request->toArray()]);

        $pirep = $this->pirepRepo->findWithoutFail($request->id);
        if($request->isMethod('post')) {
            $new_status = (int) $request->new_status;
            $pirep = $this->pirepSvc->changeStatus($pirep, $new_status);
        }

        $pirep->refresh();
        return view('admin.pireps.pirep_card', ['pirep' => $pirep]);
    }
}
