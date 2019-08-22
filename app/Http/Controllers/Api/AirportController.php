<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\Airport as AirportResource;
use App\Repositories\AirportRepository;
use App\Services\AirportService;
use Illuminate\Http\Request;

/**
 * Class AirportController
 */
class AirportController extends Controller
{
    private $airportRepo;
    private $airportSvc;

    /**
     * AirportController constructor.
     *
     * @param AirportRepository $airportRepo
     * @param AirportService $airportSvc
     */
    public function __construct(
        AirportRepository $airportRepo,
        AirportService $airportSvc
    ) {
        $this->airportRepo = $airportRepo;
        $this->airportSvc = $airportSvc;
    }

    /**
     * Return all the airports, paginated
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $where = [];
        if ($request->filled('hub')) {
            $where['hub'] = $request->get('hub');
        }

        $airports = $this->airportRepo
            ->whereOrder($where, 'icao', 'asc')
            ->paginate();

        return AirportResource::collection($airports);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index_hubs()
    {
        $where = [
            'hub' => true,
        ];

        $airports = $this->airportRepo
            ->whereOrder($where, 'icao', 'asc')
            ->paginate();

        return AirportResource::collection($airports);
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param $id
     *
     * @return AirportResource
     */
    public function get($id)
    {
        $id = strtoupper($id);

        return new AirportResource($this->airportRepo->find($id));
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param $id
     *
     * @return AirportResource
     */
    public function lookup($id)
    {
        $airport = $this->airportSvc->lookupAirport($id);
        return new AirportResource(collect($airport));
    }
}
