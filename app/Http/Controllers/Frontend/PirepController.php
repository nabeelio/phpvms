<?php

namespace App\Http\Controllers\Frontend;

use App\Facades\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePirepRequest;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\PirepFieldRepository;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;
use App\Services\GeoService;
use App\Services\PIREPService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;


class PirepController extends Controller
{
    private $airlineRepo,
            $pirepRepo,
            $airportRepo,
            $pirepFieldRepo,
            $geoSvc,
            $pirepSvc,
            $subfleetRepo,
            $userSvc;

    /**
     * PirepController constructor.
     * @param AirlineRepository $airlineRepo
     * @param PirepRepository $pirepRepo
     * @param AirportRepository $airportRepo
     * @param PirepFieldRepository $pirepFieldRepo
     * @param GeoService $geoSvc
     * @param SubfleetRepository $subfleetRepo
     * @param PIREPService $pirepSvc
     * @param UserService $userSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        PirepRepository $pirepRepo,
        AirportRepository $airportRepo,
        PirepFieldRepository $pirepFieldRepo,
        GeoService $geoSvc,
        SubfleetRepository $subfleetRepo,
        PIREPService $pirepSvc,
        UserService $userSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->pirepRepo = $pirepRepo;
        $this->airportRepo = $airportRepo;
        $this->subfleetRepo = $subfleetRepo;
        $this->pirepFieldRepo = $pirepFieldRepo;

        $this->geoSvc = $geoSvc;
        $this->pirepSvc = $pirepSvc;
        $this->userSvc = $userSvc;
    }

    /**
     * Dropdown with aircraft grouped by subfleet
     * @param null $user
     * @return array
     */
    public function aircraftList($user=null, $add_blank=false)
    {
        $aircraft = [];
        $subfleets = $this->userSvc->getAllowableSubfleets($user);

        if($add_blank) {
            $aircraft[''] = '';
        }

        foreach ($subfleets as $subfleet) {
            $tmp = [];
            foreach ($subfleet->aircraft as $ac) {
                $tmp[$ac->id] = $ac['name'] . ' - ' . $ac['registration'];
            }

            $aircraft[$subfleet->name] = $tmp;
        }

        return $aircraft;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $where = [['user_id', $user->id]];
        $where[] = ['state', '<>', PirepState::CANCELLED];

        $this->pirepRepo->pushCriteria(new WhereCriteria($request, $where));
        $pireps = $this->pirepRepo->orderBy('created_at', 'desc')->paginate();

        return $this->view('pireps.index', [
            'user' => $user,
            'pireps' => $pireps,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return $this->view('pireps.create', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'aircraft' => $this->aircraftList($user, true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'pirep_fields' => $this->pirepFieldRepo->all(),
            'field_values' => [],
        ]);
    }

    /**
     * @param CreatePirepRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(CreatePirepRequest $request)
    {
        // Create the main PIREP
        $pirep = new Pirep($request->post());
        $pirep->user_id = Auth::user()->id;

        # Make sure this isn't a duplicate
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        if ($dupe_pirep !== false) {
            flash()->error('This PIREP has already been filed.');
            return redirect(route('frontend.pireps.create'))->withInput();
        }

        // Any special fields
        $hours = (int) $request->input('hours', 0);
        $minutes = (int) $request->input('minutes', 0);
        $pirep->flight_time = Utils::hoursToMinutes($hours) + $minutes;

        // The custom fields from the form
        $custom_fields = [];
        $pirep_fields = $this->pirepFieldRepo->all();
        foreach ($pirep_fields as $field) {
            if(!$request->filled($field->slug)) {
                continue;
            }

            $custom_fields[] = [
                'name' => $field->name,
                'value' => $request->input($field->slug),
                'source' => PirepSource::MANUAL
            ];
        }

        Log::info('PIREP Custom Fields', $custom_fields);
        $pirep = $this->pirepSvc->create($pirep, $custom_fields);
        $this->pirepSvc->saveRoute($pirep);

        return redirect(route('frontend.pireps.show', ['id' => $pirep->id]));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $pirep = $this->pirepRepo->find($id);
        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('frontend.pirep.index'));
        }

        $map_featuers = $this->geoSvc->pirepGeoJson($pirep);

        return $this->view('pireps.show', [
            'pirep' => $pirep,
            'map_features' => $map_featuers,
        ]);
    }
}
