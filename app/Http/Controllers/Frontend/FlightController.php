<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\Bid;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Services\GeoService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class FlightController
 * @package App\Http\Controllers\Frontend
 */
class FlightController extends Controller
{
    private $airlineRepo,
            $airportRepo,
            $flightRepo,
            $geoSvc;

    /**
     * FlightController constructor.
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param FlightRepository  $flightRepo
     * @param GeoService        $geoSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        FlightRepository $flightRepo,
        GeoService $geoSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
        $this->geoSvc = $geoSvc;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $where = [
            'active' => true,
			'airline_id' =>  Auth::user()->airline_id,
        ];

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo
            ->orderBy('flight_number', 'asc')
            ->orderBy('route_leg', 'asc')
			->orderBy('route_code', 'asc')
            ->paginate();

        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        return view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Find the user's bids and display them
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bids(Request $request)
    {
        $user = Auth::user();

        $flights = $user->flights()->paginate();
        $saved_flights = $flights->pluck('id')->toArray();

        return view('flights.index', [
            'title'    => trans_choice('frontend.flights.mybid', 2),
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Make a search request using the Repository search
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function search(Request $request)
    {
		$request['airline_id'] = Auth::user()->airline_id;
		
        $flights = $this->flightRepo->searchCriteria($request)
			->orderBy('flight_number', 'asc')
			->orderBy('route_leg', 'asc')
			->orderBy('route_code', 'asc')
			->paginate();
			
        $saved_flights = Bid::where('user_id', Auth::id())
            ->pluck('flight_id')->toArray();

        return view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights'  => $flights,
            'saved'    => $saved_flights,
        ]);
    }

    /**
     * Show the flight information page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
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
