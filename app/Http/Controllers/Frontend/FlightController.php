<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\FlightRepository;
use App\Http\Controllers\AppBaseController;

use App\Models\UserFlight;
use Mockery\Exception;

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

        // TODO: PAGINATION

        $flights = $this->flightRepo->findWhere($where);
        $saved_flights = UserFlight::where('user_id', Auth::id())
                         ->pluck('flight_id')->toArray();

        return $this->view('flights.index', [
            'flights' => $flights,
            'saved' => $saved_flights,
        ]);
    }

    public function search(Request $request)
    {
        $where = ['active' => true];
        $flights = $this->flightRepo->findWhere($where);

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
        $action = $request->input('action');

        $cols = ['user_id' => $user_id,  'flight_id' => $flight_id];

        if(strtolower($action) == 'save') {
            $uf = UserFlight::create($cols);
            $uf->save();

            return response()->json([
                'id' => $uf->id,
                'message' => 'Saved!',
            ]);
        }

        elseif (strtolower($action) == 'remove') {
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
