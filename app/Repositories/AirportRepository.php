<?php

namespace App\Repositories;

use App\Models\Airport;


class AirportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'icao'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Airport::class;
    }
}
