<?php

namespace App\Repositories;

use App\Models\Flight;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\Traits\CacheableRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CacheableInterface;

class FlightRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'arr_airport_id',
        'dpt_airport_id',
        'flight_number' => 'like',
        'route' => 'like',
        'notes' => 'like',
    ];

    public function model()
    {
        return Flight::class;
    }

    /**
     * Create the search criteria and return this with the stuff pushed
     * @param FormRequest $request
     * @param bool $only_active
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function searchCriteria(Request $request, bool $only_active=true)
    {
        $where = [
            'active' => $only_active,
        ];

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
