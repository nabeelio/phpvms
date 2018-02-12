<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Repositories\AirlineRepository;
use App\Http\Resources\Airline as AirlineResource;

class AirlineController extends RestController
{
    protected $airlineRepo;

    public function __construct(AirlineRepository $airlineRepo) {
        $this->airlineRepo = $airlineRepo;
    }

    /**
     * Return all the airlines, paginated
     */
    public function index(Request $request)
    {
        $airports = $this->airlineRepo
            ->whereOrder(['active' => true], 'name', 'asc')
            ->paginate(50);

        return AirlineResource::collection($airports);
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     * @param $id
     * @return AirlineResource
     */
    public function get($id)
    {
        $id = strtoupper($id);
        return new AirlineResource($this->airlineRepo->find($id));
    }
}
