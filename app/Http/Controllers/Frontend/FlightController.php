<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\FlightRepository;
use App\Http\Controllers\AppBaseController;


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
        return $this->view('flights.index', [
            'flights' => $flights,
        ]);
    }

    public function show($id)
    {

    }

    public function search(Request $request) {
        $where = ['active' => true];
        $flights = $this->flightRepo->findWhere($where);

        // TODO: PAGINATION

        return $this->view('flights.index', [
            'flights' => $flights,
        ]);
    }

    public function update()
    {

    }
}
