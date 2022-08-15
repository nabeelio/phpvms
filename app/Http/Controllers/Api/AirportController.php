<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\Airport as AirportResource;
use App\Http\Resources\AirportDistance as AirportDistanceResource;
use App\Repositories\AirportRepository;
use App\Services\AirportService;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AirportController
 */
class AirportController extends Controller
{
    private AirportRepository $airportRepo;
    private AirportService $airportSvc;

    /**
     * AirportController constructor.
     *
     * @param AirportRepository $airportRepo
     * @param AirportService    $airportSvc
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

        $this->airportRepo->pushCriteria(new RequestCriteria($request));

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

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param $fromIcao
     * @param $toIcao
     *
     * @return AirportDistanceResource
     */
    public function distance($fromIcao, $toIcao)
    {
        $distance = $this->airportSvc->calculateDistance($fromIcao, $toIcao);
        return new AirportDistanceResource([
            'fromIcao' => $fromIcao,
            'toIcao'   => $toIcao,
            'distance' => $distance,
        ]);
    }
}
