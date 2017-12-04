<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\FlightRepository;
use App\Http\Controllers\AppBaseController;

use App\Models\Flight;
use App\Models\UserFlight;
use App\Repositories\Criteria\WhereCriteria;
use Mockery\Exception;
use Prettus\Repository\Criteria\RequestCriteria;

class FlightController extends AppBaseController
{
    private $flightRepo;

    public function __construct(FlightRepository $flightRepo)
    {
        $this->flightRepo = $flightRepo;
    }

    public function index(Request $request)
    {
        $where = ['active' => true];

        // default restrictions on the flights shown. Handle search differently
        if (config('phpvms.only_flights_from_current')) {
            $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
        }

        $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
        $flights = $this->flightRepo->paginate();

        $saved_flights = UserFlight::where('user_id', Auth::id())
                         ->pluck('flight_id')->toArray();

        return $this->view('flights.index', [
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
        $where = ['active' => true];

        $this->flightRepo->pushCriteria(new RequestCriteria($request));
        $flights = $this->flightRepo->paginate();

        // TODO: PAGINATION

        $saved_flights = UserFlight::where('user_id', Auth::id())
                         ->pluck('flight_id')->toArray();

        return $this->view('flights.index', [
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
            $uf = UserFlight::create($cols);
            $uf->save();

            return response()->json([
                'id' => $uf->id,
                'message' => 'Saved!',
            ]);
        }

        elseif ($action === 'remove') {
            try {
                $uf = UserFlight::where($cols)->first();
                $uf->delete();
            } catch (Exception $e) { }

            return response()->json([
                'message' => 'Deleted!'
            ]);
        }
    }

    public function update()
    {

    }

    public function show($id)
    {

    }
}
