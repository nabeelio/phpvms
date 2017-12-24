<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Services\GeoService;
use Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AppBaseController;
use App\Models\UserBid;
use App\Repositories\FlightRepository;
use App\Repositories\Criteria\WhereCriteria;

use Mockery\Exception;
use Prettus\Repository\Exceptions\RepositoryException;

class FlightController extends AppBaseController
{
    private $airlineRepo,
            $airportRepo,
            $flightRepo,
            $geoSvc;

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

    public function index(Request $request)
    {
        $where = ['active' => true];

        // default restrictions on the flights shown. Handle search differently
        if (config('phpvms.only_flights_from_current')) {
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
            } catch (Exception $e) { }

            return response()->json([
                'message' => 'Deleted!'
            ]);
        }
    }

    /**
     * Show the flight information page
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
