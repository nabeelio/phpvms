<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\Airport as AirportResource;
use App\Http\Resources\AirportDistance as AirportDistanceResource;
use App\Repositories\AirportRepository;
use App\Services\AirportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AirportController
 */
class AirportController extends Controller
{
    /**
     * AirportController constructor.
     *
     * @param AirportRepository $airportRepo
     * @param AirportService    $airportSvc
     */
    public function __construct(
        private readonly AirportRepository $airportRepo,
        private readonly AirportService $airportSvc
    ) {
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
     * @return AnonymousResourceCollection
     */
    public function index_hubs(): AnonymousResourceCollection
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
     * Return a specific airport
     *
     * @param string $id
     *
     * @return AirportResource
     */
    public function get(string $id): AirportResource
    {
        $id = strtoupper($id);

        return new AirportResource($this->airportRepo->find($id));
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param string $id
     *
     * @return AirportResource
     */
    public function lookup(string $id): AirportResource
    {
        $airport = $this->airportSvc->lookupAirport($id);
        return new AirportResource(collect($airport));
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param string $fromIcao
     * @param string $toIcao
     *
     * @return AirportDistanceResource
     */
    public function distance(string $fromIcao, string $toIcao): AirportDistanceResource
    {
        $distance = $this->airportSvc->calculateDistance($fromIcao, $toIcao);
        return new AirportDistanceResource([
            'fromIcao' => $fromIcao,
            'toIcao'   => $toIcao,
            'distance' => $distance,
        ]);
    }
}
