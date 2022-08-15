<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Controllers\Admin\Traits\Importable;
use App\Http\Requests\CreateFlightRequest;
use App\Http\Requests\UpdateFlightRequest;
use App\Models\Enums\FlightType;
use App\Models\Enums\ImportExportType;
use App\Models\Flight;
use App\Models\FlightField;
use App\Models\FlightFieldValue;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\FareRepository;
use App\Repositories\FlightFieldRepository;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use App\Services\ExportService;
use App\Services\FareService;
use App\Services\FleetService;
use App\Services\FlightService;
use App\Services\ImportService;
use App\Support\Units\Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class FlightController extends Controller
{
    use Importable;

    private AirlineRepository $airlineRepo;
    private AirportRepository $airportRepo;
    private FareRepository $fareRepo;
    private FlightRepository $flightRepo;
    private FlightFieldRepository $flightFieldRepo;
    private FareService $fareSvc;
    private FlightService $flightSvc;
    private ImportService $importSvc;
    private SubfleetRepository $subfleetRepo;

    /**
     * FlightController constructor.
     *
     * @param AirlineRepository     $airlineRepo
     * @param AirportRepository     $airportRepo
     * @param FareRepository        $fareRepo
     * @param FlightRepository      $flightRepo
     * @param FlightFieldRepository $flightFieldRepo
     * @param FareService           $fareSvc
     * @param FlightService         $flightSvc
     * @param ImportService         $importSvc
     * @param SubfleetRepository    $subfleetRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FareRepository $fareRepo,
        FlightRepository $flightRepo,
        FlightFieldRepository $flightFieldRepo,
        FareService $fareSvc,
        FlightService $flightSvc,
        ImportService $importSvc,
        SubfleetRepository $subfleetRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->fareRepo = $fareRepo;
        $this->flightRepo = $flightRepo;
        $this->flightFieldRepo = $flightFieldRepo;
        $this->fareSvc = $fareSvc;
        $this->flightSvc = $flightSvc;
        $this->importSvc = $importSvc;
        $this->subfleetRepo = $subfleetRepo;
    }

    /**
     * Save any custom fields found
     *
     * @param Flight  $flight
     * @param Request $request
     */
    protected function saveCustomFields(Flight $flight, Request $request): void
    {
        $custom_fields = [];
        $flight_fields = FlightField::all();
        foreach ($flight_fields as $field) {
            if (!$request->filled($field->slug)) {
                continue;
            }

            $custom_fields[] = [
                'name'  => $field->name,
                'value' => $request->input($field->slug),
            ];
        }

        Log::info('PIREP Custom Fields', $custom_fields);
        $this->flightSvc->updateCustomFields($flight, $custom_fields);
    }

    /**
     * @param $flight
     *
     * @return array
     */
    protected function getAvailSubfleets($flight)
    {
        $retval = [];

        $flight->refresh();
        $all_aircraft = $this->subfleetRepo->all();
        $avail_fleets = $all_aircraft->except($flight->subfleets->modelKeys());

        foreach ($avail_fleets as $ac) {
            $retval[$ac->id] = '['.$ac->airline->icao.']&nbsp;'.$ac->type.' - '.$ac->name;
        }

        return $retval;
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
        $flights = $this->flightRepo
            ->with(['dpt_airport', 'arr_airport', 'alt_airport', 'airline'])
            ->searchCriteria($request, false)
            ->orderBy('flight_number', 'asc')
            ->paginate();

        return view('admin.flights.index', [
            'flights'  => $flights,
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
        ]);
    }

    /**
     * Show the form for creating a new Flight.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.flights.create', [
            'flight'        => null,
            'days'          => 0,
            'flight_fields' => $this->flightFieldRepo->all(),
            'airlines'      => $this->airlineRepo->selectBoxList(),
            'airports'      => $this->airportRepo->selectBoxList(true, false),
            'alt_airports'  => $this->airportRepo->selectBoxList(true),
            'flight_types'  => FlightType::select(true),
        ]);
    }

    /**
     * @param CreateFlightRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateFlightRequest $request)
    {
        try {
            $flight = $this->flightSvc->createFlight($request->all());
            Flash::success('Flight saved successfully.');

            return redirect(route('admin.flights.edit', $flight->id));
        } catch (\Exception $e) {
            Log::error($e);
            Flash::error($e->getMessage());
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($flight);

        return view('admin.flights.show', [
            'flight'          => $flight,
            'flight_fields'   => $this->flightFieldRepo->all(),
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');

            return redirect(route('admin.flights.index'));
        }

        $time = new Time($flight->flight_time);

        $flight->hours = $time->hours;
        $flight->minutes = $time->minutes;

        return view('admin.flights.edit', [
            'flight'          => $flight,
            'days'            => $flight->days,
            'flight_fields'   => $this->flightFieldRepo->all(),
            'airlines'        => $this->airlineRepo->selectBoxList(),
            'airports'        => $this->airportRepo->selectBoxList(),
            'alt_airports'    => $this->airportRepo->selectBoxList(true),
            'avail_fares'     => $this->getAvailFares($flight),
            'avail_subfleets' => $this->getAvailSubfleets($flight),
            'flight_types'    => FlightType::select(true),
        ]);
    }

    /**
     * @param                     $id
     * @param UpdateFlightRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, UpdateFlightRequest $request)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        try {
            $this->flightSvc->updateFlight($flight, $request->all());
            Flash::success('Flight updated successfully.');

            return redirect(route('admin.flights.index'));
        } catch (\Exception $e) {
            Log::error($e);
            Flash::error($e->getMessage());
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * @param $id
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $flight = $this->flightRepo->findWithoutFail($id);

        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $this->flightSvc->deleteFlight($flight);

        Flash::success('Flight deleted successfully.');
        return redirect(route('admin.flights.index'));
    }

    /**
     * Run the flight exporter
     *
     * @param Request $request
     *
     * @throws \League\Csv\Exception
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $exporter = app(ExportService::class);

        $where = [];
        $file_name = 'flights.csv';
        if ($request->input('airline_id')) {
            $airline_id = $request->input('airline_id');
            $where['airline_id'] = $airline_id;
            $file_name = 'flights-'.$airline_id.'.csv';
        }
        $flights = $this->flightRepo->where($where)->orderBy('airline_id')->orderBy('flight_number')->orderBy('route_code')->orderBy('route_leg')->get();

        $path = $exporter->exportFlights($flights);
        return response()->download($path, $file_name, ['content-type' => 'text/csv'])->deleteFileAfterSend(true);
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $logs = [
            'success' => [],
            'errors'  => [],
        ];

        if ($request->isMethod('post')) {
            $logs = $this->importFile($request, ImportExportType::FLIGHTS);
        }

        return view('admin.flights.import', [
            'logs' => $logs,
        ]);
    }

    /**
     * @param $flight
     *
     * @return mixed
     */
    protected function return_fields_view($flight)
    {
        $flight->refresh();
        return view('admin.flights.flight_fields', [
            'flight'        => $flight,
            'flight_fields' => $this->flightFieldRepo->all(),
        ]);
    }

    /**
     * @param         $flight_id
     * @param Request $request
     *
     * @return mixed
     */
    public function field_values($flight_id, Request $request)
    {
        $flight = $this->flightRepo->findWithoutFail($flight_id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add custom field to flight
        if ($request->isMethod('post')) {
            Log::info('Adding new flight field, flight: '.$flight_id, $request->input());

            $field = new FlightFieldValue();
            $field->flight_id = $flight_id;
            $field->name = $request->input('name');
            $field->value = $request->input('value');
            $field->save();
        } elseif ($request->isMethod('put')) {
            Log::info('Updating flight field, flight: '.$flight_id, $request->input());
            $field = FlightFieldValue::where([
                'name'      => $request->input('name'),
                'flight_id' => $flight_id,
            ])->first();

            if (!$field) {
                Log::info('Field not found, creating new');
                $field = new FlightFieldValue();
                $field->name = $request->input('name');
            }

            $field->flight_id = $flight_id;
            $field->value = $request->input('value');
            $field->save();
        // update the field value
        } // remove custom field from flight
        elseif ($request->isMethod('delete')) {
            Log::info('Deleting flight field, flight: '.$flight_id, $request->input());
            if ($flight_id && $request->input('field_id')) {
                FlightFieldValue::destroy($request->input('field_id'));
            }
        }

        return $this->return_fields_view($flight);
    }

    /**
     * @param $flight
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function return_subfleet_view($flight)
    {
        $avail_subfleets = $this->getAvailSubfleets($flight);

        return view('admin.flights.subfleets', [
            'flight'          => $flight,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return mixed
     */
    public function subfleets($id, Request $request)
    {
        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        $fleetSvc = app(FleetService::class);

        // add aircraft to flight
        $subfleet = $this->subfleetRepo->findWithoutFail($request->subfleet_id);
        if (!$subfleet) {
            return $this->return_subfleet_view($flight);
        }

        if ($request->isMethod('post')) {
            $fleetSvc->addSubfleetToFlight($subfleet, $flight);
        } // remove aircraft from flight
        elseif ($request->isMethod('delete')) {
            $fleetSvc->removeSubfleetFromFlight($subfleet, $flight);
        }

        return $this->return_subfleet_view($flight);
    }

    /**
     * Get all the fares that haven't been assigned to a given subfleet
     *
     * @param mixed $flight
     *
     * @return mixed
     * @return array
     */
    protected function getAvailFares($flight)
    {
        $retval = [];
        $all_fares = $this->fareRepo->all();
        $avail_fares = $all_fares->except($flight->fares->modelKeys());
        foreach ($avail_fares as $fare) {
            $retval[$fare->id] = $fare->name.' (base price: '.$fare->price.')';
        }

        return $retval;
    }

    /**
     * @param Flight $flight
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function return_fares_view(Flight $flight)
    {
        $flight->refresh();

        return view('admin.flights.fares', [
            'flight'      => $flight,
            'avail_fares' => $this->getAvailFares($flight),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fares(Request $request)
    {
        $id = $request->id;

        $flight = $this->flightRepo->findWithoutFail($id);
        if (empty($flight)) {
            return $this->return_fares_view($flight);
        }

        if ($request->isMethod('get')) {
            return $this->return_fares_view($flight);
        }

        if ($request->isMethod('delete')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $this->fareSvc->delFareFromFlight($flight, $fare);

            return $this->return_fares_view($flight);
        }

        $this->validate($request, [
            'value' => 'nullable',  // regex:/([\d%]*)/
        ]);

        /*
         * update specific fare data
         */
        if ($request->isMethod('post')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $this->fareSvc->setForFlight($flight, $fare);
        } // update the pivot table with overrides for the fares
        elseif ($request->isMethod('put')) {
            $override = [];
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $override[$request->name] = $request->value;
            $this->fareSvc->setForFlight($flight, $fare, $override);
        } // dissassociate fare from teh aircraft

        return $this->return_fares_view($flight);
    }
}
