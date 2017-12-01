<?php

namespace App\Repositories;

use App\Models\Flight;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class FlightRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'arr_airport_id',
        'dpt_airport_id',
        'flight_number' => 'like',
        'route' => 'like',
        'notes' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Flight::class;
    }
}
