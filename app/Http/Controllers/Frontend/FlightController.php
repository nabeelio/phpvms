<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserBid;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @param FlightRepository $flightRepo
     * @param GeoService $geoSvc
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
        $where = ['active' => true];

        // default restrictions on the flights shown. Handle search differently
        if (setting('pilots.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        try {
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $flights = $this->flightRepo->paginate();

        $saved_flights = UserBid::where('user_id', Auth::id())
                         ->pluck('flight_id')->toArray();

        return $this->view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights' => $flights,
            'saved' => $saved_flights,
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
        $flights = $this->flightRepo->searchCriteria($request)->paginate();

        $saved_flights = UserBid::where('user_id', Auth::id())
                         ->pluck('flight_id')->toArray();

        return $this->view('flights.index', [
            'airlines' => $this->airlineRepo->selectBoxList(true),
            'airports' => $this->airportRepo->selectBoxList(true),
            'flights' => $flights,
            'saved' => $saved_flights,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function save(Request $request)
    {
        $user_id = Auth::id();
        $flight_id = $request->input('flight_id');
        $action = strtolower($request->input('action'));

        $cols = ['user_id' => $user_id,  'flight_id' => $flight_id];

        if($action === 'save') {
            $uf = UserBid::create($cols);
            $uf->save();

            return response()->json([
                'id' => $uf->id,
                'message' => 'Saved!',
            ]);
        }

        elseif ($action === 'remove') {
            try {
                $uf = UserBid::where($cols)->first();
                $uf->delete();
            } catch (\Exception $e) { }

            return response()->json([
                'message' => 'Deleted!'
            ]);
        }
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

        return $this->view('flights.show', [
            'flight' => $flight,
            'map_features' => $map_features,
        ]);
    }
}
