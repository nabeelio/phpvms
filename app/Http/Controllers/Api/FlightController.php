<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FlightCollection;
use App\Repositories\Criteria\WhereCriteria;
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
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $where = ['active' => true];
        if (setting('pilots.only_flights_from_current', false)) {
            $where['dpt_airport_id'] = $user->curr_airport_id;
        }

        $flights = $this->flightRepo
                    ->whereOrder($where, 'flight_number', 'asc')
                    ->paginate();

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

        return new FlightResource($flight);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        try {
            $where = ['active' => true];
            if (setting('pilots.only_flights_from_current')) {
                $where['dpt_airport_id'] = Auth::user()->curr_airport_id;
            }

            $this->flightRepo->searchCriteria($request);
            $this->flightRepo->pushCriteria(new RequestCriteria($request));
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where));
            $flights = $this->flightRepo->paginate();
        } catch (RepositoryException $e) {
            return response($e, 503);
        }

        foreach ($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
        }

        return FlightResource::collection($flights);
    }
}
