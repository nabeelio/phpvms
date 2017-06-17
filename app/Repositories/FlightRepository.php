<?php

namespace App\Repositories;

use App\Models\Flight;
use InfyOm\Generator\Common\BaseRepository;

class FlightRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'dpt_airport_id',
        'arr_airport_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Flight::class;
    }
}
