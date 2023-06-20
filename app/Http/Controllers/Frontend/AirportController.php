<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Repositories\AirportRepository;
use App\Repositories\FlightRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class AirportController extends Controller
{
    public function __construct(
        private readonly AirportRepository $airportRepo,
        private readonly FlightRepository $flightRepo
    ) {
    }

    /**
     * Show the airport
     *
     * @param string  $id
     * @param Request $request
     *
     * @return RedirectResponse|View
     */
    public function show(string $id, Request $request): RedirectResponse|View
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
