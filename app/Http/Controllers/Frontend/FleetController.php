<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Repositories\AircraftRepository;

class FleetController extends Controller
{
    private $aircraftRepo;

    /**
     * SubfleetController constructor.
     *
     * @param AircraftRepository $aircraftRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
    }

    public function showFleet()
    {
        $w = [];
        $aircrafts = $this->aircraftRepo->with(['subfleet'])->whereOrder($w, 'registration', 'asc');
        $aircrafts = $aircrafts->all();
        
        return view('flights.fleet', [
            'aircrafts' => $aircrafts
        ]);
    }
}
