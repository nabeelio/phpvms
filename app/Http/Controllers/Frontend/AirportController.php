<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Repositories\AirportRepository;
use App\Repositories\FlightRepository;
use Flash;
use Request;

/**
 * Class HomeController
 */
class AirportController extends Controller
{
    private AirportRepository $airportRepo;
    private FlightRepository $flightRepo;

    public function __construct(
        AirportRepository $airportRepo,
        FlightRepository $flightRepo
    ) {
        $this->airportRepo = $airportRepo;
        $this->flightRepo = $flightRepo;
    }

    /**
     * Show the airport
     *
     * @param mixed $id
     *
     * @return mixed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id, Request $request)
    {
        $id = strtoupper($id);
        $with_flights = ['airline', 'arr_airport', 'dpt_airport'];

        $airport = $this->airportRepo->with('files')->where('id', $id)->first();
        if (!$airport) {
            Flash::error('Airport not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $inbound_flights = $this->flightRepo
            ->with($with_flights)
            ->findWhere([
                'arr_airport_id' => $id,
                'active'         => 1,
            ])->all();

        $outbound_flights = $this->flightRepo
            ->with($with_flights)
            ->findWhere([
                'dpt_airport_id' => $id,
                'active'         => 1,
            ])->all();

        return view('airports.show', [
            'airport'          => $airport,
            'inbound_flights'  => $inbound_flights,
            'outbound_flights' => $outbound_flights,
        ]);
    }
}
