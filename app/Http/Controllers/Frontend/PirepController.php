<?php

namespace App\Http\Controllers\Frontend;

use App\Facades\Utils;
use App\Models\Enums\PirepSource;
use App\Repositories\Criteria\WhereCriteria;
use App\Services\GeoService;
use App\Services\PIREPService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Pirep;
use App\Models\PirepField;

use App\Http\Controllers\Controller;
use App\Repositories\AirlineRepository;
use App\Repositories\AircraftRepository;
use App\Repositories\AirportRepository;
use App\Repositories\PirepRepository;
use App\Repositories\PirepFieldRepository;


class PirepController extends Controller
{
    private $airlineRepo,
            $aircraftRepo,
            $pirepRepo,
            $airportRepo,
            $pirepFieldRepo,
            $geoSvc,
            $pirepSvc;

    public function __construct(
        AirlineRepository $airlineRepo,
        PirepRepository $pirepRepo,
        AircraftRepository $aircraftRepo,
        AirportRepository $airportRepo,
        PirepFieldRepository $pirepFieldRepo,
        GeoService $geoSvc,
        PIREPService $pirepSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
        $this->airportRepo = $airportRepo;
        $this->pirepFieldRepo = $pirepFieldRepo;

        $this->geoSvc = $geoSvc;
        $this->pirepSvc = $pirepSvc;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $where = ['user_id' => $user->id];
        $this->pirepRepo->pushCriteria(new WhereCriteria($request, $where));
        $pireps = $this->pirepRepo->orderBy('created_at', 'desc')->paginate();

        return $this->view('pireps.index', [
            'user' => $user,
            'pireps' => $pireps,
        ]);
    }

    public function create()
    {
        return $this->view('pireps.create', [
            'airports' => $this->airportRepo->selectBoxList(),
            'airlines' => $this->airlineRepo->selectBoxList(),
            'aircraft' => $this->aircraftRepo->selectBoxList(),
            'pirepfields' => $this->pirepFieldRepo->all(),
            'fieldvalues' => [],
        ]);
    }

    public function store(Request $request)
    {
        $pirep_fields = $request->all();

        // Create the main PIREP
        $pirep = new Pirep($pirep_fields);

        // Any special fields
        $pirep->pilot()->associate(Auth::user());
        $pirep->flight_time = ((int) Utils::hoursToMinutes($request['hours']))
                            + ((int) $request['minutes']);

        // The custom fields from the form
        $custom_fields = [];
        foreach($pirep_fields as $field_name => $field_val)
        {
            if (strpos($field_name, 'field_') === false) {
                continue;
            }

            $field_id = explode('field_', $field_name)[1];
            $cfield = PirepField::find($field_id);

            $custom_fields[] = [
                'name' => $cfield->name,
                'value' => $field_val,
                'source' => PirepSource::MANUAL
            ];
        }

        $pirep = $this->pirepSvc->create($pirep, $custom_fields);

        //Flash::success('PIREP submitted successfully!');
        return redirect(route('frontend.pireps.index'));
    }

    public function show($id)
    {
        #$pirep = Pirep::where('id', $id);
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
