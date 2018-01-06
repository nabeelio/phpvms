<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;

use App\Repositories\FlightRepository;
use App\Http\Resources\Flight as FlightResource;
use Prettus\Repository\Exceptions\RepositoryException;


class FlightController extends RestController
{
    protected $flightRepo;

    public function __construct(FlightRepository $flightRepo) {
        $this->flightRepo = $flightRepo;
    }

    /**
     * Return all the flights, paginated
     */
    public function index(Request $request)
    {
        $flights = $this->flightRepo
                        ->orderBy('flight_number', 'asc')
                        ->paginate(50);

        return FlightResource::collection($flights);
    }

    public function get($id)
    {
        $flight = $this->flightRepo->find($id);
        FlightResource::withoutWrapping();
        return new FlightResource($flight);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        try {
            $this->flightRepo->searchCriteria($request);
            $this->flightRepo->pushCriteria(new RequestCriteria($request));
            $flights = $this->flightRepo->paginate();
        } catch (RepositoryException $e) {
            return response($e, 503);
        }

        return FlightResource::collection($flights);
    }
}
