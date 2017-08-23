<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Pirep;
use App\Models\PirepField;

use App\Http\Controllers\Controller;
use App\Repositories\AirlineRepository;
use App\Repositories\AircraftRepository;
use App\Repositories\PirepRepository;


class PirepController extends Controller
{
    public function __construct(
        AirlineRepository $airlineRepo,
        PirepRepository $pirepRepo,
        AircraftRepository $aircraftRepo)
    {
        $this->airlineRepo = $airlineRepo;
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function airportList()
    {
        # TODO: Cache
        $retval = [];
        $airports = Airport::all();
        foreach($airports as $airport) {
            $retval[$airport->id] = $airport->icao.' - '.$airport->name;
        }

        return $retval;
    }

    public function aircraftList()
    {
        $retval = [];
        $aircraft = $this->aircraftRepo->all();

        foreach ($aircraft as $ac) {
            $retval[$ac->id] = $ac->subfleet->name.' - '.$ac->name.' ('.$ac->registration.')';
        }

        return $retval;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $pireps = Pirep::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return $this->view('pireps.index', [
            'user' => $user,
            'pireps' => $pireps,
        ]);
    }

    public function create()
    {
        $aircraft = $this->aircraftList();
        $airports = $this->airportList();

        return $this->view('pireps.create', [
            'airports' => $airports,
            'airlines' => Airline::all()->pluck('name', 'id'),
            'aircraft' => $aircraft,
            'pirepfields' => PirepField::all(),
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

        $pirepSvc = app('\App\Services\PIREPService');
        $pirep = $pirepSvc->create($pirep, $custom_fields);

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
