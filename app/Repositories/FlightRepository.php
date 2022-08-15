<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Flight;
use App\Repositories\Criteria\WhereCriteria;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class FlightRepository
 */
class FlightRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'arr_airport_id',
        'callsign',
        'distance',
        'dpt_airport_id',
        'flight_time',
        'flight_type',
        'flight_number' => 'like',
        'route_code'    => 'like',
        'route_leg'     => 'like',
        'route'         => 'like',
        'notes'         => 'like',
    ];

    public function model(): string
    {
        return Flight::class;
    }

    /**
     * Find a flight based on the given criterea
     *
     * @param      $airline_id
     * @param      $flight_num
     * @param null $route_code
     * @param null $route_leg
     *
     * @return mixed
     */
    public function findFlight($airline_id, $flight_num, $route_code = null, $route_leg = null)
    {
        $where = [
            'airline_id'    => $airline_id,
            'flight_number' => $flight_num,
            'active'        => true,
        ];

        if (filled($route_code)) {
            $where['route_code'] = $route_code;
        }

        if (filled($route_leg)) {
            $where['route_leg'] = $route_leg;
        }

        return $this->findWhere($where);
    }

    /**
     * Create the search criteria and return this with the stuff pushed
     *
     * @param Request $request
     * @param bool    $only_active
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return $this
     */
    public function searchCriteria(Request $request, bool $only_active = true): self
    {
        $where = [];
        $relations = [];

        if ($only_active === true) {
            $where['active'] = $only_active;
            $where['visible'] = $only_active;
        }

        if ($request->filled('flight_id')) {
            $where['id'] = $request->input('flight_id');
        }

        if ($request->filled('airline_id')) {
            $where['airline_id'] = $request->input('airline_id');
        }

        if ($request->filled('flight_number')) {
            $where['flight_number'] = $request->input('flight_number');
        }

        if ($request->filled('callsign')) {
            $where['callsign'] = $request->input('callsign');
        }

        if ($request->filled('flight_type') && $request->input('flight_type') !== '0') {
            $where['flight_type'] = $request->input('flight_type');
        }

        if ($request->filled('route_code')) {
            $where['route_code'] = $request->input('route_code');
        }

        if ($request->filled('dpt_airport_id')) {
            $where['dpt_airport_id'] = strtoupper($request->input('dpt_airport_id'));
        }

        if ($request->filled('dep_icao')) {
            $where['dpt_airport_id'] = strtoupper($request->input('dep_icao'));
        }

        if ($request->filled('arr_airport_id')) {
            $where['arr_airport_id'] = strtoupper($request->input('arr_airport_id'));
        }

        if ($request->filled('arr_icao')) {
            $where['arr_airport_id'] = strtoupper($request->input('arr_icao'));
        }

        // Distance, greater than
        if ($request->filled('dgt')) {
            $where[] = ['distance', '>=', $request->input('dgt')];
        }

        // Distance, less than
        if ($request->filled('dlt')) {
            $where[] = ['distance', '<=', $request->input('dlt')];
        }

        // Time, greater than
        if ($request->filled('tgt')) {
            $where[] = ['flight_time', '>=', $request->input('tgt')];
        }

        // Time, less than
        if ($request->filled('tlt')) {
            $where[] = ['flight_time', '<=', $request->input('tlt')];
        }

        // Do a special query for finding the child subfleets
        if ($request->filled('subfleet_id')) {
            $relations['subfleets'] = [
                'subfleets.id' => $request->input('subfleet_id'),
            ];
        }

        $this->pushCriteria(new WhereCriteria($request, $where, $relations));

        return $this;
    }
}
