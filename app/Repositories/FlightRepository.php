<?php

namespace App\Repositories;

use App\Models\Flight;
use App\Repositories\Criteria\WhereCriteria;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class FlightRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'arr_airport_id',
        'dpt_airport_id',
        'flight_number' => 'like',
        'flight_code' => 'like',
        'flight_leg' => 'like',
        'route' => 'like',
        'notes' => 'like',
    ];

    public function model()
    {
        return Flight::class;
    }

    /**
     * Find a flight based on the given criterea
     * @param $airline_id
     * @param $flight_num
     * @param null $flight_code
     * @param null $flight_leg
     * @return mixed
     */
    public function findFlight($airline_id, $flight_num, $flight_code=null, $flight_leg=null)
    {
        $where = [
            'airline_id' => $airline_id,
            'flight_num' => $flight_num,
            'active' => true,
        ];

        if(filled($flight_code)) {
            $where['flight_code'] = $flight_code;
        }

        if(filled('flight_leg')) {
            $where['flight_leg'] = $flight_leg;
        }

        return $this->findWhere($where);
    }

    /**
     * Create the search criteria and return this with the stuff pushed
     * @param Request $request
     * @param bool $only_active
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function searchCriteria(Request $request, bool $only_active=true)
    {
        $where = [];

        if($only_active === true) {
            $where['active'] = $only_active;
        }

        if ($request->filled('flight_id')) {
            $where['id'] = $request->flight_id;
        }

        if ($request->filled('airline_id')) {
            $where['airline_id'] = $request->airline_id;
        }

        if($request->filled('flight_number')) {
            $where['flight_number'] = $request->flight_number;
        }

        if ($request->filled('route_code')) {
            $where['route_code'] = $request->route_code;
        }

        if ($request->filled('dep_icao')) {
            $where['dpt_airport_id'] = $request->dep_icao;
        }

        if ($request->filled('arr_icao')) {
            $where['arr_airport_id'] = $request->arr_icao;
        }

        $this->pushCriteria(new WhereCriteria($request, $where));
        return $this;
    }
}
