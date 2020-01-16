<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Bid;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Repositories\SubfleetRepository;
use App\Services\GeoService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class FlightController extends Controller
{
    private $airlineRepo;
    private $airportRepo;
    private $flightRepo;
    private $subfleetRepo;
    private $geoSvc;

    /**
     * FlightController constructor.
     *
     * @param AirlineRepository  $airlineRepo
     * @param AirportRepository  $airportRepo
     * @param FlightRepository   $flightRepo
     * @param GeoService         $geoSvc
     * @param SubfleetRepository $subfleetRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FlightRepository $flightRepo,
        GeoService $geoSvc,
        SubfleetRepository $subfleetRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
        $this->geoSvc = $geoSvc;
        $this->subfleetRepo = $subfleetRepo;
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
        return $this->search($request);
    }

    /**
     * Make a search request using the Repository search
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return mixed
     */
    public function search(Request $request)
    {
        $where = [
            'active'  => true,
            'visible' => true,
        ];

        if (setting('pilots.restrict_to_company')) {
            $where['airline_id'] = Auth::user()->airline_id;
        }

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        $this->flightRepo->resetCriteria();

        try {
            $this->flightRepo->searchCriteria($request);
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
            $this->flightRepo->pushCriteria(new RequestCriteria($request));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo->searchCriteria($request)
            ->with(['dpt_airport', 'arr_airport', 'airline'])
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        return view('flights.index', [
            'airlines'      => $this->airlineRepo->selectBoxList(true),
            'airports'      => $this->airportRepo->selectBoxList(true),
            'flights'       => $flights,
            'saved'         => $saved_flights,
            'subfleets'     => $this->subfleetRepo->selectBoxList(true),
            'flight_number' => $request->input('flight_number'),
            'arr_icao'      => $request->input('arr_icao'),
            'dep_icao'      => $request->input('dep_icao'),
            'subfleet_id'   => $request->input('subfleet_id'),
        ]);
    }

    /**
     * Find the user's bids and display them
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bids(Request $request)
    {
        $user = Auth::user();

        $flights = $user->flights()->paginate();
        $saved_flights = $flights->pluck('id')->toArray();

        return view('flights.index', [
            'title'    => trans_choice('flights.mybid', 2),
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Show the flight information page
     *
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $flight = $this->flightRepo->find($id);
        if (empty($flight)) {
            Flash::error('Flight not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $map_features = $this->geoSvc->flightGeoJson($flight);

        return view('flights.show', [
            'flight'       => $flight,
            'map_features' => $map_features,
        ]);
    }
}
