<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Exceptions\AssetNotFound;
use App\Http\Resources\Flight as FlightResource;
use App\Http\Resources\Navdata as NavdataResource;
use App\Models\SimBrief;
use App\Models\User;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\FlightRepository;
use App\Services\FareService;
use App\Services\FlightService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class FlightController extends Controller
{
    private FareService $fareSvc;
    private FlightRepository $flightRepo;
    private FlightService $flightSvc;

    /**
     * @param FareService      $fareSvc
     * @param FlightRepository $flightRepo
     * @param FlightService    $flightSvc
     */
    public function __construct(
        FareService $fareSvc,
        FlightRepository $flightRepo,
        FlightService $flightSvc
    ) {
        $this->fareSvc = $fareSvc;
        $this->flightRepo = $flightRepo;
        $this->flightSvc = $flightSvc;
    }

    /**
     * Return all the flights, paginated
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return $this->search($request);
    }

    /**
     * @param $id
     *
     * @return FlightResource
     */
    public function get($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\Flight $flight */
        $flight = $this->flightRepo->with([
            'airline',
            'fares',
            'subfleets',
            'subfleets.aircraft',
            'subfleets.fares',
            'field_values',
            'simbrief' => function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            },
        ])->find($id);

        $flight = $this->flightSvc->filterSubfleets($user, $flight);
        $flight = $this->fareSvc->getReconciledFaresForFlight($flight);

        return new FlightResource($flight);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function search(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $where = [
            'active'  => true,
            'visible' => true,
        ];

        // Allow the option to bypass some of these restrictions for the searches
        if (!$request->filled('ignore_restrictions') || $request->get('ignore_restrictions') === '0') {
            if (setting('pilots.restrict_to_company')) {
                $where['airline_id'] = $user->airline_id;
            }

            if (setting('pilots.only_flights_from_current')) {
                $where['dpt_airport_id'] = $user->curr_airport_id;
            }
        }

        try {
            $this->flightRepo->resetCriteria();
            $this->flightRepo->searchCriteria($request);
            $this->flightRepo->pushCriteria(new WhereCriteria($request, $where, [
                'airline' => ['active' => true],
            ]));

            $this->flightRepo->pushCriteria(new RequestCriteria($request));

            $flights = $this->flightRepo
                ->with([
                    'airline',
                    'fares',
                    'subfleets',
                    'subfleets.aircraft',
                    'subfleets.fares',
                    'field_values',
                    'simbrief' => function ($query) use ($user) {
                        return $query->where('user_id', $user->id);
                    },
                ])
                ->paginate();
        } catch (RepositoryException $e) {
            return response($e, 503);
        }

        // TODO: Remove any flights here that a user doesn't have permissions to
        foreach ($flights as $flight) {
            $this->flightSvc->filterSubfleets($user, $flight);
            $this->fareSvc->getReconciledFaresForFlight($flight);
        }

        return FlightResource::collection($flights);
    }

    /**
     * Output the flight briefing from simbrief or whatever other format
     *
     * @param string $id The flight ID
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function briefing(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $w = [
            'id' => $id,
        ];

        /** @var SimBrief $simbrief */
        $simbrief = SimBrief::where($w)->first();

        if ($simbrief === null) {
            throw new AssetNotFound(new Exception('Flight briefing not found'));
        }

        /*if ($simbrief->user_id !== $user->id) {
            throw new Unauthorized(new Exception('User cannot access another user\'s simbrief'));
        }*/

        return response($simbrief->acars_xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Get a flight's route
     *
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route($id, Request $request)
    {
        $flight = $this->flightRepo->find($id);
        $route = $this->flightSvc->getRoute($flight);

        return NavdataResource::collection($route);
    }
}
