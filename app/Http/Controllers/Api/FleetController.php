<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Repositories\AircraftRepository;
use App\Repositories\SubfleetRepository;

use App\Http\Resources\Aircraft as AircraftResource;
use App\Http\Resources\Subfleet as SubfleetResource;

class FleetController extends RestController
{
    protected $aircraftRepo, $subfleetRepo;

    public function __construct(
        AircraftRepository $aircraftRepo,
        SubfleetRepository $subfleetRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    /**
     * Return all the subfleets and the aircraft and any other associated data
     * Paginated
     */
    public function index()
    {
        $subfleets = $this->subfleetRepo
                          ->with(['aircraft', 'airline', 'fares', 'ranks'])
                          ->paginate(50);

        return SubfleetResource::collection($subfleets);
    }

    /**
     * Get a specific aircraft. Query string required to specify the tail
     * /api/aircraft/XYZ?type=registration
     * @param $id
     * @param Request $request
     * @return AircraftResource
     */
    public function get_aircraft($id, Request $request)
    {
        $where = [];
        if($request->filled('type')) {
            $where[$request->get('type')] = $id;
        } else {
            $where['id'] = $id;
        }

        $all_aircraft = $this->aircraftRepo->all();
        $aircraft = $this->aircraftRepo
                         ->with(['subfleet'])
                         ->findWhere($where)
                         ->first();

        AircraftResource::withoutWrapping();
        return new AircraftResource($aircraft);
    }
}
