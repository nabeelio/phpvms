<?php

namespace App\Http\Controllers\Admin;

use Log;
use Flash;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;

use App\Services\PIREPService;

use App\Models\PirepComment;
use App\Models\Enums\PirepState;

use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Repositories\AircraftRepository;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\PirepRepository;
use App\Facades\Utils;


class PirepController extends BaseController
{
    private $airportRepo,
            $airlineRepo,
            $pirepRepo,
            $aircraftRepo,
            $pirepSvc;

    public function __construct(
        AirportRepository $airportRepo,
        AirlineRepository $airlineRepo,
        AircraftRepository $aircraftRepo,
        PirepRepository $pirepRepo,
        PIREPService $pirepSvc
    ) {
        $this->airportRepo = $airportRepo;
        $this->airlineRepo = $airlineRepo;
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
            ->findWhere(['status' => PirepState::PENDING])
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

        $pirep->flight_time = ((int) Utils::hoursToMinutes($request['hours']))
                            + ((int) $request['minutes']);

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

        $hms = Utils::minutesToTimeParts($pirep->flight_time);
        $pirep->hours = $hms['h'];
        $pirep->minutes = $hms['m'];

        return view('admin.pireps.edit', [
            'pirep' => $pirep,
            'airports' => $this->airportRepo->selectBoxList(),
            'airlines' => $this->airlineRepo->selectBoxList(),
            'aircraft' => $this->aircraftRepo->selectBoxList(),
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

        $pirep->flight_time = ((int) Utils::hoursToMinutes($request['hours']))
                            + ((int) $request['minutes']);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $attrs = $request->all();
        $orig_route = $pirep->route;
        $pirep = $this->pirepRepo->update($attrs, $id);

        // A route change in the PIREP, so update the saved points in the ACARS table
        if($pirep->route !== $orig_route) {
            $this->pirepSvc->saveRoute($pirep);
        }

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
        Log::info('PIREP state update call', [$request->toArray()]);

        $pirep = $this->pirepRepo->findWithoutFail($request->id);
        if($request->isMethod('post')) {
            $new_status = (int) $request->new_status;
            $pirep = $this->pirepSvc->changeState($pirep, $new_status);
        }

        $pirep->refresh();
        return view('admin.pireps.pirep_card', ['pirep' => $pirep]);
    }

    /**
     * Add a comment to the Pirep
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function comments($id, Request $request)
    {
        $user = Auth::user();
        $pirep = $this->pirepRepo->findWithoutFail($request->id);
        if ($request->isMethod('post')) {
            $comment = new PirepComment([
                'user_id' => $user->id,
                'pirep_id' => $pirep->id,
                'comment' => $request->get('comment'),
            ]);

            $comment->save();
            $pirep->refresh();
        }

        if($request->isMethod('delete')) {
            $comment = PirepComment::find($request->get('comment_id'));
            $comment->delete();
            $pirep->refresh();
        }

        return view('admin.pireps.comments', [
            'pirep' => $pirep,
        ]);
    }
}
