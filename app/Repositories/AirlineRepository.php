<?php

namespace App\Repositories;

use App\Models\Airline;

class AirlineRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Airline::class;
    }
}
