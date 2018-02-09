<?php

namespace App\Http\Controllers\Api;

use App\Services\FlightService;
use Auth;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

use App\Services\UserService;
use App\Repositories\FlightRepository;
use App\Http\Resources\Flight as FlightResource;

/**
 * Class FlightController
 * @package App\Http\Controllers\Api
 */
class FlightController extends RestController
{
    protected $flightRepo, $flightSvc, $userSvc;

    public function __construct(
        FlightRepository $flightRepo,
        FlightService $flightSvc,
        UserService $userSvc
    ) {
        $this->flightRepo = $flightRepo;
        $this->flightSvc = $flightSvc;
        $this->userSvc = $userSvc;
    }

    /**
     * Return all the flights, paginated
     */
    public function index(Request $request)
    {
        $flights = $this->flightRepo
                        ->orderBy('flight_number', 'asc')
                        ->paginate(50);

        $user = Auth::user();
        foreach($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
        }

        return FlightResource::collection($flights);
    }

    /**
     * @param $id
     * @return FlightResource
     */
    public function get($id)
    {
        $flight = $this->flightRepo->find($id);
        $this->flightSvc->filterSubfleets(Auth::user(), $flight);

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

        $user = Auth::user();
        foreach ($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
        }

        return FlightResource::collection($flights);
    }
}
