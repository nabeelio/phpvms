<?php

namespace App\Repositories;

use App\Models\Aircraft;

class AircraftRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable
        = [
            'icao',
            'name',
            'full_name',
            'registration',
            'active',
        ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Aircraft::class;
    }

    public function findByICAO($icao)
    {
        return $this->findByField('icao', $icao)->first();
    }
}
