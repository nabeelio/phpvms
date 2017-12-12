<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\AppBaseController;
use App\Repositories\FlightRepository;
use App\Http\Resources\Flight as FlightResource;
use Prettus\Repository\Exceptions\RepositoryException;


class FlightController extends AppBaseController
{
    protected $flightRepo;

    public function __construct(FlightRepository $flightRepo) {
        $this->flightRepo = $flightRepo;
    }

    public function get($id)
    {
        $flight = $this->flightRepo->find($id);
        FlightResource::withoutWrapping();
        return new FlightResource($flight);
    }

    public function search(Request $request)
    {
        try {
            $flights = $this->flightRepo->searchCriteria($request)->paginate();
        } catch (RepositoryException $e) {
            return response($e, 503);
        }

        return FlightResource::collection($flights);
    }
}
