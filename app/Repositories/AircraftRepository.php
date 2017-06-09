<?php

namespace App\Repositories;

use App\Models\Aircraft;
use InfyOm\Generator\Common\BaseRepository;

class AircraftRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
}
