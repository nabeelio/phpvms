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
        'route' => 'like',
        'notes' => 'like',
    ];

    public function model()
    {
        return Flight::class;
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
        $where = [
            'active' => $only_active,
        ];

        if ($request->airline) {
            $where['airline_id'] = $request->airline;
        }

        if ($request->depICAO) {
            $where['dpt_airport_id'] = $request->depICAO;
        }

        if ($request->arrICAO) {
            $where['dpt_airport_id'] = $request->arrICAO;
        }

        $this->pushCriteria(new WhereCriteria($request, $where));
        return $this;
    }
}
