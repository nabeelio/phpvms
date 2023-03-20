<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Fare;
use App\Models\Pirep;
use App\Models\PirepFare;
use App\Models\SimBrief;
use App\Models\User;
use App\Repositories\AircraftRepository;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Repositories\PirepFieldRepository;
use App\Repositories\PirepRepository;
use App\Services\FareService;
use App\Services\GeoService;
use App\Services\PirepService;
use App\Services\SimBriefService;
use App\Services\UserService;
use App\Support\Units\Fuel;
use App\Support\Units\Time;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class PirepController extends Controller
{
    private AircraftRepository $aircraftRepo;
    private AirlineRepository $airlineRepo;
    private FareService $fareSvc;
    private FlightRepository $flightRepo;
    private GeoService $geoSvc;
    private PirepRepository $pirepRepo;
    private AirportRepository $airportRepo;
    private PirepFieldRepository $pirepFieldRepo;
    private PirepService $pirepSvc;
    private UserService $userSvc;

    /**
     * @param AircraftRepository   $aircraftRepo
     * @param AirlineRepository    $airlineRepo
     * @param AirportRepository    $airportRepo
     * @param FareService          $fareSvc
     * @param FlightRepository     $flightRepo
     * @param GeoService           $geoSvc
     * @param PirepRepository      $pirepRepo
     * @param PirepFieldRepository $pirepFieldRepo
     * @param PirepService         $pirepSvc
     * @param UserService          $userSvc
     */
    public function __construct(
        AircraftRepository $aircraftRepo,
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FareService $fareSvc,
        FlightRepository $flightRepo,
        GeoService $geoSvc,
        PirepRepository $pirepRepo,
        PirepFieldRepository $pirepFieldRepo,
        PirepService $pirepSvc,
        UserService $userSvc
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->airlineRepo = $airlineRepo;
        $this->pirepRepo = $pirepRepo;
        $this->airportRepo = $airportRepo;
        $this->pirepFieldRepo = $pirepFieldRepo;

        $this->fareSvc = $fareSvc;
        $this->flightRepo = $flightRepo;
        $this->geoSvc = $geoSvc;
        $this->pirepSvc = $pirepSvc;
        $this->userSvc = $userSvc;
    }

    /**
     * Dropdown with aircraft grouped by subfleet
     *
     * @param mixed $add_blank
     *
     * @return array
     */
    public function aircraftList($add_blank = false)
    {
        $user = Auth::user();
        $user_loc = filled($user->curr_airport_id) ? $user->curr_airport_id : $user->home_airport_id;
        $location_check = setting('pireps.only_aircraft_at_dpt_airport', false);

        $aircraft = [];
        $subfleets = $this->userSvc->getAllowableSubfleets($user);

        if ($add_blank) {
            $aircraft[''] = '';
        }

        foreach ($subfleets as $subfleet) {
            $tmp = [];
            foreach ($subfleet->aircraft->when($location_check, function ($query) use ($user_loc) {
                return $query->where('airport_id', $user_loc);
            }) as $ac) {
                $tmp[$ac->id] = $ac['name'].' - '.$ac['registration'];
            }

            $aircraft[$subfleet->type] = $tmp;
        }

        return $aircraft;
    }

    /**
     * Save any custom fields found
     *
     * @param Request $request
     */
    protected function saveCustomFields(Request $request): array
    {
        $fields = [];
        $pirep_fields = $this->pirepFieldRepo->all();
        foreach ($pirep_fields as $field) {
            if (!$request->filled($field->slug)) {
                continue;
            }

            $fields[] = [
                'name'   => $field->name,
                'slug'   => $field->slug,
                'value'  => $request->input($field->slug),
                'source' => PirepSource::MANUAL,
            ];
        }

        Log::info('PIREP Custom Fields', $fields);

        return $fields;
    }

    /**
     * Save the fares that have been specified/saved
     *
     * @param Pirep   $pirep
     * @param Request $request
     *
     * @throws \Exception
     */
    protected function saveFares(Pirep $pirep, Request $request)
    {
        $fares = [];
        if (!$pirep->aircraft) {
            return;
        }

        foreach ($pirep->aircraft->subfleet->fares as $fare) {
            $field_name = 'fare_'.$fare->id;
            if (!$request->filled($field_name)) {
                $count = 0;
            } else {
                $count = $request->input($field_name);
            }

            $fares[] = new PirepFare([
                'fare_id' => $fare->id,
                'count'   => $count,
            ]);
        }

        $this->fareSvc->saveForPirep($pirep, $fares);
    }

    /**
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $where = [['user_id', $user->id]];
        $where[] = ['state', '<>', PirepState::CANCELLED];
        $with = ['aircraft', 'airline', 'arr_airport', 'comments', 'dpt_airport'];

        $this->pirepRepo->with($with)
            ->pushCriteria(new WhereCriteria($request, $where));
        $pireps = $this->pirepRepo->orderBy('created_at', 'desc')->paginate();

        return view('pireps.index', [
            'user'   => $user,
            'pireps' => $pireps,
        ]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $with = [
            'acars_logs',
            'aircraft.airline',
            'airline.journal',
            'arr_airport',
            'comments',
            'dpt_airport',
            'fares.fare',
            'transactions',
            'simbrief',
            'user.rank',
        ];

        $pirep = $this->pirepRepo->with($with)->find($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('frontend.pirep.index'));
        }

        $map_features = $this->geoSvc->pirepGeoJson($pirep);

        return view('pireps.show', [
            'pirep'        => $pirep,
            'map_features' => $map_features,
            'user'         => Auth::user(),
        ]);
    }

    /**
     * Return the fares form for a given aircraft
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fares(Request $request)
    {
        $aircraft_id = $request->input('aircraft_id');
        $aircraft = $this->aircraftRepo->find($aircraft_id);

        return view('pireps.fares', [
            'aircraft'  => $aircraft,
            'read_only' => false,
        ]);
    }

    /**
     * Create a new flight report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $pirep = null;

        // See if request has a ?flight_id, so we can pre-populate the fields from the flight
        // Makes filing easier, but we can also more easily find a bid and close it
        if ($request->has('flight_id')) {
            $flight = $this->flightRepo->find($request->input('flight_id'));
            $pirep = Pirep::fromFlight($flight);
        }

        /**
         * They have a SimBrief ID, load that up and figure out the flight that it's from
         */
        $fare_values = [];
        $simbrief = null;
        $simbrief_id = null;
        $aircraft = null;
        if ($request->has('sb_id')) {
            $simbrief_id = $request->input('sb_id');
            $simbrief = SimBrief::find($simbrief_id);
            $pirep = Pirep::fromSimBrief($simbrief);

            $aircraft = $simbrief->aircraft;
            $aircraft_list[$aircraft->subfleet->name] = [];
            $aircraft_list[$aircraft->subfleet->name][$aircraft->id] = $aircraft->name.' - '.$aircraft->registration;

            // Convert the fare data into the expected output format
            if (!empty($simbrief->fare_data)) {
                $fare_values = json_decode($simbrief->fare_data, true);
                $fares = [];
                $fare_data = json_decode($simbrief->fare_data, true);
                foreach ($fare_data as $fare) {
                    $fares[] = new Fare($fare);
                }

                $aircraft->subfleet->fares = collect($fares);
            }

        // TODO: Set more fields from the Simbrief to the PIREP form
        } else {
            $aircraft_list = $this->aircraftList(true);
        }

        return view('pireps.create', [
            'aircraft'      => $aircraft,
            'pirep'         => $pirep,
            'read_only'     => false,
            'airline_list'  => $this->airlineRepo->selectBoxList(true),
            'aircraft_list' => $aircraft_list,
            'airport_list'  => $this->airportRepo->selectBoxList(true),
            'pirep_fields'  => $this->pirepFieldRepo->all(),
            'field_values'  => [],
            'fare_values'   => $fare_values,
            'simbrief_id'   => $simbrief_id,
            'simbrief'      => $simbrief,
        ]);
    }

    /**
     * @param CreatePirepRequest $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreatePirepRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $pirep = new Pirep($request->post());
        $pirep->user_id = $user->id;

        $attrs = $request->all();
        $attrs['submit'] = strtolower($attrs['submit']);

        if ($attrs['submit'] === 'submit') {
            // Are they allowed at this airport?
            if (setting('pilots.only_flights_from_current')
                && $user->curr_airport_id !== $pirep->dpt_airport_id) {
                Log::info(
                    'Pilot '.$user->id
                    .' not at departure airport (curr='.$user->curr_airport_id
                    .', dpt='.$pirep->dpt_airport_id.')'
                );

                return $this->flashError(
                    'You are currently not at the departure airport!',
                    'frontend.pireps.create'
                );
            }

            // Can they fly this aircraft?
            if (setting('pireps.restrict_aircraft_to_rank', false)
                && !$this->userSvc->aircraftAllowed($user, $pirep->aircraft_id)) {
                Log::info('Pilot '.$user->id.' not allowed to fly aircraft');
                return $this->flashError(
                    'You are not allowed to fly this aircraft!',
                    'frontend.pireps.create'
                );
            }

            // is the aircraft in the right place?
            /* @noinspection NotOptimalIfConditionsInspection */
            // Get the aircraft
            $aircraft = $this->aircraftRepo->findWithoutFail($pirep->aircraft_id);
            if ($aircraft === null) {
                Log::error('Aircraft for PIREP not found, id='.$pirep->aircraft_id);
                return $this->flashError(
                    'The aircraft for the PIREP hasn\'t been found',
                    'frontend.pireps.create'
                );
            }

            if (setting('pireps.only_aircraft_at_dpt_airport')
                && $aircraft->airport_id !== $pirep->dpt_airport_id
            ) {
                Log::info('Aircraft '.$pirep->aircraft_id.' not at departure airport (curr='.$pirep->aircraft->airport_id.', apt='.$pirep->dpt_airport_id.')');
                return $this->flashError(
                    'This aircraft is not positioned at the departure airport!',
                    'frontend.pireps.create'
                );
            }

            // Make sure this isn't a duplicate
            $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
            if ($dupe_pirep !== false) {
                Log::info('Duplicate PIREP found');
                return $this->flashError(
                    'This PIREP has already been filed.',
                    'frontend.pireps.create'
                );
            }
        }

        // Any special fields
        $hours = (int) $request->input('hours', 0);
        $minutes = (int) $request->input('minutes', 0);
        $pirep->flight_time = Time::hoursToMinutes($hours) + $minutes;

        // Set the correct fuel units
        $pirep->block_fuel = Fuel::make((float) $request->input('block_fuel'), setting('units.fuel'));
        $pirep->fuel_used = Fuel::make((float) $request->input('fuel_used'), setting('units.fuel'));

        // Put the time that this is currently submitted
        $attrs['submitted_at'] = Carbon::now('UTC');
        $pirep->submitted_at = Carbon::now('UTC');

        $fields = $this->saveCustomFields($request);
        $pirep = $this->pirepSvc->create($pirep, $fields);
        $this->saveFares($pirep, $request);
        $this->pirepSvc->saveRoute($pirep);

        if ($request->has('sb_id')) {
            $brief = SimBrief::find($request->input('sb_id'));
            if ($brief !== null) {
                /** @var SimBriefService $sbSvc */
                $sbSvc = app(SimBriefService::class);
                // Keep the flight_id with SimBrief depending on the button selected
                // Save = Keep the flight_id , Submit = Remove the flight_id
                if ($attrs['submit'] === 'save') {
                    $sbSvc->attachSimbriefToPirep($pirep, $brief, true);
                } elseif ($attrs['submit'] === 'submit') {
                    $sbSvc->attachSimbriefToPirep($pirep, $brief);
                }
            }
        }

        // Depending on the button they selected, set an initial state
        // Can be saved as a draft or just submitted
        if ($attrs['submit'] === 'save') {
            if (!$pirep->read_only) {
                $pirep->state = PirepState::DRAFT;
            }

            $pirep->save();
            Flash::success('PIREP saved successfully.');
        } elseif ($attrs['submit'] === 'submit') {
            $this->pirepSvc->submit($pirep);
            Flash::success('PIREP submitted!');
        }

        return redirect(route('frontend.pireps.show', [$pirep->id]));
    }

    /**
     * Show the form for editing the specified Pirep.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        /** @var Pirep $pirep */
        $pirep = $this->pirepRepo->findWithoutFail($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('frontend.pireps.index'));
        }

        if ($pirep->user_id !== Auth::id()) {
            Flash::error('Cannot edit someone else\'s PIREP!');
            return redirect(route('admin.pireps.index'));
        }

        // Eager load the subfleet and fares under it
        if ($pirep->aircraft) {
            $pirep->aircraft->load('subfleet.fares');
        }

        $simbrief_id = null;
        if ($pirep->simbrief) {
            $simbrief_id = $pirep->simbrief->id;
        }

        $time = new Time($pirep->flight_time);
        $pirep->hours = $time->hours;
        $pirep->minutes = $time->minutes;

        // set the custom fields
        foreach ($pirep->fields as $field) {
            if ($field->slug === null) {
                $field->slug = str_slug($field->name);
            }

            $field_name = 'field_'.$field->slug;
            $pirep->{$field_name} = $field->value;
        }

        // set the fares
        foreach ($pirep->fares as $fare) {
            $field_name = 'fare_'.$fare->fare_id;
            $pirep->{$field_name} = $fare->count;
        }

        return view('pireps.edit', [
            'pirep'         => $pirep,
            'aircraft'      => $pirep->aircraft,
            'aircraft_list' => $this->aircraftList(true),
            'airline_list'  => $this->airlineRepo->selectBoxList(),
            'airport_list'  => $this->airportRepo->selectBoxList(),
            'pirep_fields'  => $this->pirepFieldRepo->all(),
            'simbrief_id'   => $simbrief_id,
        ]);
    }

    /**
     * @param                    $id
     * @param UpdatePirepRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, UpdatePirepRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Pirep $pirep */
        $pirep = $this->pirepRepo->findWithoutFail($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        if ($user->id !== $pirep->user_id) {
            Flash::error('Cannot edit someone else\'s PIREP!');
            return redirect(route('admin.pireps.index'));
        }

        $orig_route = $pirep->route;
        $attrs = $request->all();
        $attrs['submit'] = strtolower($attrs['submit']);

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

        $fields = $this->saveCustomFields($request);
        $this->pirepSvc->updateCustomFields($pirep->id, $fields);
        $this->saveFares($pirep, $request);

        if ($attrs['submit'] === 'save') {
            Flash::success('PIREP saved successfully.');
        } elseif ($attrs['submit'] === 'submit') {
            $this->pirepSvc->submit($pirep);
            Flash::success('PIREP submitted!');
        } elseif ($attrs['submit'] === 'delete' || $attrs['submit'] === 'cancel') {
            $this->pirepSvc->delete($pirep);
            Flash::success('PIREP deleted!');
            return redirect(route('frontend.pireps.index'));
        }

        return redirect(route('frontend.pireps.show', [$pirep->id]));
    }

    /**
     * Submit the PIREP
     *
     * @param         $id
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function submit($id, Request $request)
    {
        $pirep = $this->pirepRepo->findWithoutFail($id);
        if (empty($pirep)) {
            Flash::error('PIREP not found');
            return redirect(route('admin.pireps.index'));
        }

        if ($pirep->user_id !== Auth::id()) {
            Flash::error('Cannot edit someone else\'s PIREP!');
            return redirect(route('admin.pireps.index'));
        }

        $this->pirepSvc->submit($pirep);
        return redirect(route('frontend.pireps.show', [$pirep->id]));
    }
}
