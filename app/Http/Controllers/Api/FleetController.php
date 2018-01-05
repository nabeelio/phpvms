<?php

namespace App\Http\Controllers\Api;

use App\Repositories\AircraftRepository;
use App\Repositories\SubfleetRepository;

use App\Http\Resources\Subfleet as SubfleetResource;

class FleetController extends RestController
{
    protected $aircraftRepo, $subfleetRepo;

    public function __construct(
        AircraftRepository $aircraftRepo,
        SubfleetRepository $airportRepo
    ) {
        $this->aircraftRepo = $airportRepo;
        $this->subfleetRepo = $airportRepo;
    }

    /**
     * Return all the subfleets and the aircraft and any other associated data
     * Paginated
     */
    public function index()
    {
        $airports = $this->subfleetRepo
                         ->with(['aircraft', 'airline', 'fares', 'ranks'])
                         ->paginate(50);

        return SubfleetResource::collection($airports);
    }
}
