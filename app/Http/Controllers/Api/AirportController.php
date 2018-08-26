<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Airport as AirportResource;
use App\Interfaces\Controller;
use App\Repositories\AirportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Log;
use VaCentral\Airport as AirportLookup;

/**
 * Class AirportController
 */
class AirportController extends Controller
{
    private $airportRepo;

    /**
     * AirportController constructor.
     *
     * @param AirportRepository $airportRepo
     */
    public function __construct(
        AirportRepository $airportRepo
    ) {
        $this->airportRepo = $airportRepo;
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
        $airport = Cache::remember(
            config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.key').$id,
            config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.time'),
            function () use ($id) {
                try {
                    return AirportLookup::get($id);
                } catch (\VaCentral\HttpException $e) {
                    Log::error($e);
                    return [];
                }
            }
        );

        return new AirportResource(collect($airport));
    }
}
