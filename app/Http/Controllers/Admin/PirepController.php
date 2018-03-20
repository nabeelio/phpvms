<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Utils;
use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Interfaces\Controller;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\PirepComment;
use App\Repositories\AircraftRepository;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\JournalRepository;
use App\Repositories\PirepFieldRepository;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;
use App\Services\FareService;
use App\Services\PirepService;
use App\Services\UserService;
use App\Support\Units\Time;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PirepController
 * @package App\Http\Controllers\Admin
 */
class PirepController extends Controller
{
    private $airportRepo,
            $airlineRepo,
            $aircraftRepo,
            $fareSvc,
            $journalRepo,
            $pirepSvc,
            $pirepRepo,
            $pirepFieldRepo,
            $subfleetRepo,
            $userSvc;

    /**
     * PirepController constructor.
     * @param AirportRepository    $airportRepo
     * @param AirlineRepository    $airlineRepo
     * @param AircraftRepository   $aircraftRepo
     * @param FareService          $fareSvc
     * @param JournalRepository    $journalRepo
     * @param PirepRepository      $pirepRepo
     * @param PirepFieldRepository $pirepFieldRepo
     * @param PirepService         $pirepSvc
     * @param SubfleetRepository   $subfleetRepo
     * @param UserService          $userSvc
     */
    public function __construct(
        AirportRepository $airportRepo,
        AirlineRepository $airlineRepo,
        AircraftRepository $aircraftRepo,
        FareService $fareSvc,
        JournalRepository $journalRepo,
        PirepRepository $pirepRepo,
        PirepFieldRepository $pirepFieldRepo,
        PirepService $pirepSvc,
        SubfleetRepository $subfleetRepo,
        UserService $userSvc
    ) {
        $this->airportRepo = $airportRepo;
        $this->airlineRepo = $airlineRepo;
        $this->aircraftRepo = $aircraftRepo;
        $this->fareSvc = $fareSvc;
        $this->journalRepo = $journalRepo;
        $this->pirepRepo = $pirepRepo;
        $this->pirepFieldRepo = $pirepFieldRepo;
        $this->pirepSvc = $pirepSvc;
        $this->subfleetRepo = $subfleetRepo;
        $this->userSvc = $userSvc;
    }

    /**
     * Dropdown with aircraft grouped by subfleet
     * @param null $user
     * @return array
     */
    public function aircraftList($user = null)
    {
        $aircraft = [];

        if ($user === null) {
            $subfleets = $this->subfleetRepo->all();
        } else {
            $subfleets = $this->userSvc->getAllowableSubfleets($user);
        }

        foreach ($subfleets as $subfleet) {
            $tmp = [];
            foreach ($subfleet->aircraft as $ac) {
                $tmp[$ac->id] = $ac['name'].' - '.$ac['registration'];
            }

            $aircraft[$subfleet->name] = $tmp;
        }

        return $aircraft;
    }

    /**
     * Save any custom fields found
     * @param Pirep   $pirep
     * @param Request $request
     */
    protected function saveCustomFields(Pirep $pirep, Request $request): void
    {
        $custom_fields = [];
        $pirep_fields = $this->pirepFieldRepo->all();
        foreach ($pirep_fields as $field) {
            if (!$request->filled($field->slug)) {
                continue;
            }

            $custom_fields[] = [
                'name'   => $field->name,
                'value'  => $request->input($field->slug),
                'source' => PirepSource::MANUAL
            ];
        }

        Log::info('PIREP Custom Fields', $custom_fields);
        $this->pirepSvc->updateCustomFields($pirep->id, $custom_fields);
    }

    /**
     * Save the fares that have been specified/saved
     * @param Pirep   $pirep
     * @param Request $request
     * @throws \Exception
     */
    protected function saveFares(Pirep $pirep, Request $request)
    {
        $fares = [];
        foreach ($pirep->aircraft->subfleet->fares as $fare) {
            $field_name = 'fare_'.$fare->id;
            if (!$request->filled($field_name)) {
                $count = 0;
            } else {
                $count = $request->input($field_name);
            }

            $fares[] = [
                'fare_id' => $fare->id,
                'count'   => $count,
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fares);
    }

    /**
     * Return the fares form for a given aircraft
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fares(Request $request)
    {
        $aircraft_id = $request->input('aircraft_id');
        Log::info($aircraft_id);

        $aircraft = $this->aircraftRepo->find($aircraft_id);
        Log::info('aircraft', $aircraft->toArray());

        return view('admin.pireps.fares', [
            'aircraft'  => $aircraft,
            'read_only' => false,
        ]);
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
        return view('admin.pireps.create', [
            'aircraft' => $this->aircraftList(),
            'airports' => $this->airportRepo->selectBoxList(),
            'airlines' => $this->airlineRepo->selectBoxList(),
        ]);
    }

    /**
     * @param CreatePirepRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function store(CreatePirepRequest $request)
    {
        $attrs = $request->all();
        $pirep = $this->pirepRepo->create($attrs);

        $hours = (int) $attrs['hours'];
        $minutes = (int) $attrs['minutes'];
        $pirep->flight_time = Utils::hoursToMinutes($hours) + $minutes;

        $this->saveCustomFields($pirep, $request);
        $this->saveFares($pirep, $request);

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
     * @throws \InvalidArgumentException
     */
    public function edit($id)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');

            return redirect(route('admin.pireps.index'));
        }

        $time = new Time($pirep->flight_time);
        $pirep->hours = $time->hours;
        $pirep->minutes = $time->minutes;

        # Can we modify?
        $read_only = $pirep->state !== PirepState::PENDING;

        # set the custom fields
        foreach ($pirep->fields as $field) {
            $pirep->{$field->slug} = $field->value;
        }

        # set the fares
        foreach ($pirep->fares as $fare) {
            $field_name = 'fare_'.$fare->fare_id;
            $pirep->{$field_name} = $fare->count;
        }

        $journal = $this->journalRepo->getAllForObject($pirep, $pirep->airline->journal);

        return view('admin.pireps.edit', [
            'pirep'         => $pirep,
            'read_only'     => $read_only,
            'aircraft'      => $pirep->aircraft,
            'aircraft_list' => $this->aircraftList(),
            'airports_list' => $this->airportRepo->selectBoxList(),
            'airlines_list' => $this->airlineRepo->selectBoxList(),
            'journal'       => $journal,
        ]);
    }

    /**
     * @param                    $id
     * @param UpdatePirepRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function update($id, UpdatePirepRequest $request)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');

            return redirect(route('admin.pireps.index'));
        }

        $orig_route = $pirep->route;
        $orig_flight_time = $pirep->flight_time;

        $attrs = $request->all();

        # Fix the time
        $attrs['flight_time'] = Time::init(
            $attrs['minutes'],
            $attrs['hours'])->getMinutes();

        $pirep = $this->pirepRepo->update($attrs, $id);

        // A route change in the PIREP, so update the saved points in the ACARS table
        if ($pirep->route !== $orig_route) {
            $this->pirepSvc->saveRoute($pirep);
        }

        $this->saveCustomFields($pirep, $request);
        $this->saveFares($pirep, $request);

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
     * Change or update the PIREP status. Just return the new actionbar
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function status(Request $request)
    {
        Log::info('PIREP state update call', [$request->toArray()]);

        $pirep = $this->pirepRepo->findWithoutFail($request->id);
        if ($request->isMethod('post')) {
            $new_status = (int) $request->post('new_status');
            $pirep = $this->pirepSvc->changeState($pirep, $new_status);
        }

        $pirep->refresh();

        return view('admin.pireps.actions', ['pirep' => $pirep, 'on_edit_page' => false]);
    }

    /**
     * Add a comment to the Pirep
     * @param         $id
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function comments($id, Request $request)
    {
        $user = Auth::user();
        $pirep = $this->pirepRepo->findWithoutFail($request->id);
        if ($request->isMethod('post')) {
            $comment = new PirepComment([
                'user_id'  => $user->id,
                'pirep_id' => $pirep->id,
                'comment'  => $request->get('comment'),
            ]);

            $comment->save();
            $pirep->refresh();
        }

        if ($request->isMethod('delete')) {
            $comment = PirepComment::find($request->get('comment_id'));
            $comment->delete();
            $pirep->refresh();
        }

        return view('admin.pireps.comments', [
            'pirep' => $pirep,
        ]);
    }
}
