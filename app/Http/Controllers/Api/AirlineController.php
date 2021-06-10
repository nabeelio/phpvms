<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\Airline as AirlineResource;
use App\Repositories\AirlineRepository;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    private $airlineRepo;

    /**
     * AirlineController constructor.
     *
     * @param AirlineRepository $airlineRepo
     */
    public function __construct(AirlineRepository $airlineRepo)
    {
        $this->airlineRepo = $airlineRepo;
    }

    /**
     * Return all the airlines, paginated
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $airports = $this->airlineRepo->whereOrder(['active' => true], 'name');

        return AirlineResource::collection($airports);
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     *
     * @param $id
     *
     * @return AirlineResource
     */
    public function get($id)
    {
        $id = strtoupper($id);

        return new AirlineResource($this->airlineRepo->find($id));
    }
}
