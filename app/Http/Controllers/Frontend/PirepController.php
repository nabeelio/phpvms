<?php

namespace App\Http\Controllers\Frontend;

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
            $pirepSvc;

    public function __construct(
        AirlineRepository $airlineRepo,
        PirepRepository $pirepRepo,
        AircraftRepository $aircraftRepo,
        AirportRepository $airportRepo,
        PirepFieldRepository $pirepFieldRepo,
        PIREPService $pirepSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
        $this->airportRepo = $airportRepo;
        $this->pirepFieldRepo = $pirepFieldRepo;
        $this->pirepSvc = $pirepSvc;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $pireps = Pirep::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate();

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
        $pirep->flight_time = ((int)$pirep_fields['hours'] * 60 * 60)
                            + ((int)$pirep_fields['minutes'] * 60);

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
                'source' => config('enums.sources.MANUAL')
            ];
        }

        $pirep = $this->pirepSvc->create($pirep, $custom_fields);

        //Flash::success('PIREP submitted successfully!');
        return redirect(route('frontend.pireps.index'));
    }

    public function show($id)
    {
        $pirep = Pirep::where('id', $id);
        return $this->view('pireps.show', [
            'pirep' => $pirep,
        ]);
    }
}
