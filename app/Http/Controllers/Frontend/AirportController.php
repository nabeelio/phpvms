<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\User;
use App\Repositories\AirportRepository;
use App\Repositories\FlightRepository;
use Flash;
use Illuminate\Database\QueryException;
use Request;

/**
 * Class HomeController
 * @package App\Http\Controllers\Frontend
 */
class AirportController extends Controller
{
    private $airportRepo,
            $flightRepo;

    public function __construct(
        AirportRepository $airportRepo,
        FlightRepository $flightRepo
    ) {
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
    }

    /**
     * Show the airport
     */
    public function show($id, Request $request)
    {
        $id = strtoupper($id);

        $airport = $this->airportRepo->find($id);
        if (empty($airport)) {
            Flash::error('Airport not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $inbound_flights = $this->flightRepo->findWhere([
            'arr_airport_id' => $id,
        ])->all();

        $outbound_flights = $this->flightRepo->findWhere([
            'dpt_airport_id' => $id,
        ])->all();

        return view('airports.show', [
            'airport' => $airport,
            'inbound_flights' => $inbound_flights,
            'outbound_flights' => $outbound_flights,
        ]);
    }
}
