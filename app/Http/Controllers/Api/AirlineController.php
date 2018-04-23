<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Airline as AirlineResource;
use App\Interfaces\Controller;
use App\Repositories\AirlineRepository;
use Illuminate\Http\Request;

/**
 * Class AirlineController
 * @package App\Http\Controllers\Api
 */
class AirlineController extends Controller
{
    private $airlineRepo;

    /**
     * AirlineController constructor.
     * @param AirlineRepository $airlineRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo
    ) {
        $this->airlineRepo = $airlineRepo;
    }

    /**
     * Return all the airlines, paginated
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        #$this->airlineRepo->pushCriteria(new RequestCriteria($request));
        $airports = $this->airlineRepo
            ->whereOrder(['active' => true], 'name', 'asc')
            ->paginate();

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
