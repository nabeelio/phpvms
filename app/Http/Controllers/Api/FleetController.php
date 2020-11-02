<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\Aircraft as AircraftResource;
use App\Http\Resources\Subfleet as SubfleetResource;
use App\Repositories\AircraftRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;

/**
 * Class FleetController
 */
class FleetController extends Controller
{
    private $aircraftRepo;
    private $subfleetRepo;

    /**
     * FleetController constructor.
     *
     * @param AircraftRepository $aircraftRepo
     * @param SubfleetRepository $subfleetRepo
     */
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
            ->paginate();

        return SubfleetResource::collection($subfleets);
    }

    /**
     * Get a specific aircraft. Query string required to specify the tail
     * /api/aircraft/XYZ?type=registration
     *
     * @param         $id
     * @param Request $request
     *
     * @return AircraftResource
     */
    public function get_aircraft($id, Request $request)
    {
        $where = [];
        if ($request->filled('type')) {
            $where[$request->get('type')] = $id;
        } else {
            $where['id'] = $id;
        }

        $aircraft = $this->aircraftRepo
            ->with(['subfleet', 'subfleet.fares'])
            ->findWhere($where)
            ->first();

        return new AircraftResource($aircraft);
    }

    /**
     * Get aircrafts from actual airport.
     * State, status and rank are optional
     * /api/aircraft/airport/ICAO?state=0&status=B&rank=idrank
     *
     * @param         $id
     * @param Request $request
     *
     * @return AircraftResource
     */
    public function get_airport($id, Request $request)
    {
        $aircrafts = $this->aircraftRepo
            ->with(['subfleet', 'subfleet.fares', 'subfleet.ranks']);

        if ($request->filled('rank')) {
            $searchRank = function ($query) use ($request) {
                $query->where('id', $request->get('rank'));
            };
            $aircrafts->whereHas('subfleet.ranks', $searchRank);
        }

        $where = ['airport_id' => $id];
        if ($request->filled('state')) {
            $where['state'] = $request->get('state');
        }
        if ($request->filled('status')) {
            $where['status'] = $request->get('status');
        }
        $aircrafts->findWhere($where);

        return AircraftResource::collection($aircrafts);
    }
}
