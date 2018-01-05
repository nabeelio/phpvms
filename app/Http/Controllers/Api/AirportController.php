<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Repositories\AirportRepository;
use App\Http\Resources\Airport as AirportResource;

use VaCentral\Airport as AirportLookup;

class AirportController extends RestController
{
    protected $airportRepo;

    public function __construct(
        AirportRepository $airportRepo
    ) {
        $this->airportRepo = $airportRepo;
    }

    /**
     * Return all the airports, paginated
     */
    public function index(Request $request)
    {
        $where = [];
        if ($request->filled('hub')) {
            $where['hub'] = $request->get('hub');
        }

        $airports = $this->airportRepo
            ->whereOrder($where, 'icao', 'asc')
            ->paginate(50);

        return AirportResource::collection($airports);
    }

    public function index_hubs()
    {
        $where = [
            'hub' => true,
        ];

        $airports = $this->airportRepo
            ->whereOrder($where, 'icao', 'asc')
            ->paginate(50);

        return AirportResource::collection($airports);
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     * @param $id
     * @return AirportResource
     */
    public function get($id)
    {
        $id = strtoupper($id);
        AirportResource::withoutWrapping();
        return new AirportResource($this->airportRepo->find($id));
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     * @param $id
     * @return AirportResource
     */
    public function lookup($id)
    {
        $airport = Cache::remember(
            config('cache.keys.AIRPORT_VACENTRAL_LOOKUP.key') . $id,
            config('cache.keys.RANKS_PILOT_LIST.time'),
            function () use ($id) {
                return AirportLookup::get($id);
            }
        );

        return new AirportResource(collect($airport));
    }
}
