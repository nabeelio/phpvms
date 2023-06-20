<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\PirepComment;
use App\Models\User;
use App\Repositories\AircraftRepository;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\JournalRepository;
use App\Repositories\PirepFieldRepository;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;
use App\Services\PirepService;
use App\Services\UserService;
use App\Support\Units\Time;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class PirepController extends Controller
{
    /**
     * PirepController constructor.
     *
     * @param AirportRepository    $airportRepo
     * @param AirlineRepository    $airlineRepo
     * @param AircraftRepository   $aircraftRepo
     * @param JournalRepository    $journalRepo
     * @param PirepRepository      $pirepRepo
     * @param PirepFieldRepository $pirepFieldRepo
     * @param PirepService         $pirepSvc
     * @param SubfleetRepository   $subfleetRepo
     * @param UserService          $userSvc
     */
    public function __construct(
        private readonly AirportRepository $airportRepo,
        private readonly AirlineRepository $airlineRepo,
        private readonly AircraftRepository $aircraftRepo,
        private readonly JournalRepository $journalRepo,
        private readonly PirepRepository $pirepRepo,
        private readonly PirepFieldRepository $pirepFieldRepo,
        private readonly PirepService $pirepSvc,
        private readonly SubfleetRepository $subfleetRepo,
        private readonly UserService $userSvc
    ) {
    }

    /**
     * Dropdown with aircraft grouped by subfleet
     *
     * @param ?User $user
     *
     * @return array
     */
    public function aircraftList(?User $user = null): array
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

            $aircraft[$subfleet->type] = $tmp;
        }

        return $aircraft;
    }

    /**
     * Save any custom fields found
     *
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
                'source' => PirepSource::MANUAL,
            ];
        }

        Log::info('PIREP Custom Fields', $custom_fields);
        $this->pirepSvc->updateCustomFields($pirep->id, $custom_fields);
    }

    /**
     * Save the fares that have been specified/saved
     *
     * @param Pirep   $pirep
     * @param Request $request
     *
     * @throws \Exception
     */
    protected function saveFares(Pirep $pirep, Request $request): void
    {
        $fields = ['count', 'price'];
        foreach ($pirep->fares as $fare) {
            foreach ($fields as $f) {
                $field_name = 'fare_'.$fare->id.'_'.$f;
                if ($request->filled($field_name)) {
                    $val = $request->input($field_name);
                    $fare->{$f} = $val;
                }
            }

            $fare->save();
        }
    }

    /**
     * Return the fares form for a given aircraft
     *
     * @param Request $request
     *
     * @return View
     */
    public function fares(Request $request): View
    {
        $aircraft_id = $request->input('aircraft_id');
        Log::info($aircraft_id);

        $aircraft = $this->aircraftRepo->find($aircraft_id);
        Log::info('aircraft', $aircraft->toArray());

        return view('admin.pireps.fares', [
            'pirep'     => null,
            'aircraft'  => $aircraft,
            'read_only' => false,
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $criterea = new RequestCriteria($request);
        $this->pirepRepo->pushCriteria($criterea);

        $pireps = $this->pirepRepo
            ->with(['airline', 'aircraft', 'dpt_airport', 'arr_airport'])
            ->whereNotInOrder('state', [
                PirepState::CANCELLED,
                PirepState::DRAFT,
                PirepState::IN_PROGRESS,
            ], 'created_at', 'desc')
            ->paginate();

        return view('admin.pireps.index', [
            'pireps' => $pireps,
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return View
     */
    public function pending(Request $request): View
    {
        $criterea = new RequestCriteria($request);
        $this->pirepRepo->pushCriteria($criterea);

        $pireps = $this->pirepRepo
            ->findWhere(['status' => PirepState::PENDING])
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('admin.pireps.index', [
            'pireps' => $pireps,
        ]);
    }

    /**
     * Show the form for creating a new Pirep.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.pireps.create', [
            'aircraft' => $this->aircraftList(),
            'airports' => $this->airportRepo->selectBoxList(),
            'airlines' => $this->airlineRepo->selectBoxList(),
        ]);
    }

    /**
     * @param CreatePirepRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function store(CreatePirepRequest $request): RedirectResponse
    {
        $attrs = $request->all();
        $pirep = $this->pirepRepo->create($attrs);

        $hours = (int) $attrs['hours'];
        $minutes = (int) $attrs['minutes'];
        $pirep->flight_time = Time::hoursToMinutes($hours) + $minutes;

        $this->saveCustomFields($pirep, $request);
        $this->saveFares($pirep, $request);

        Flash::success('Pirep saved successfully.');

        return redirect(route('admin.pireps.index'));
    }

    /**
     * Display the specified Pirep.
     *
     * @param string $id
     *
     * @return RedirectResponse|View
     */
    public function show(string $id): RedirectResponse|View
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
     * @param string $id
     *
     * @throws \InvalidArgumentException
     *
     * @return RedirectResponse|View
     */
    public function edit(string $id): RedirectResponse|View
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $time = new Time($pirep->flight_time);
        $pirep->hours = $time->hours;
        $pirep->minutes = $time->minutes;

        // set the custom fields
        foreach ($pirep->fields as $field) {
            $field_name = 'field_'.$field->slug;
            $pirep->{$field_name} = $field->value;
        }

        // set the fares
        foreach ($pirep->fares as $fare) {
            $field_name = 'fare_'.$fare->fare_id;
            $pirep->{$field_name} = $fare->count;
        }

        $journal = $this->journalRepo->getAllForObject($pirep, $pirep->airline->journal);

        return view('admin.pireps.edit', [
            'pirep'         => $pirep,
            'aircraft'      => $pirep->aircraft,
            'aircraft_list' => $this->aircraftList(),
            'airports_list' => $this->airportRepo->selectBoxList(),
            'airlines_list' => $this->airlineRepo->selectBoxList(),
            'journal'       => $journal,
        ]);
    }

    /**
     * @param string             $id
     * @param UpdatePirepRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function update(string $id, UpdatePirepRequest $request): RedirectResponse
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $orig_route = $pirep->route;
        $orig_flight_time = $pirep->flight_time;

        $attrs = $request->all();

        // Fix the time
        $attrs['flight_time'] = Time::init(
            $attrs['minutes'],
            $attrs['hours']
        )->getMinutes();

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
     *
     * @param string $id
     *
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $this->pirepSvc->delete($pirep);

        Flash::success('Pirep deleted successfully.');
        return redirect()->back();
    }

    /**
     * Change or update the PIREP status. Just return the new actionbar
     *
     * @param Request $request
     *
     * @return View
     */
    public function status(Request $request): View
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
     *
     * @param string  $id
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return View
     */
    public function comments(string $id, Request $request): View
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
