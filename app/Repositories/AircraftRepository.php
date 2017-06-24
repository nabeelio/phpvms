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
            'name',
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
}
