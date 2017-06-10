<?php

namespace App\Repositories;

use App\Models\AircraftClass;
use InfyOm\Generator\Common\BaseRepository;

class AircraftClassRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'class',
        'name',
        'notes'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AircraftClass::class;
    }
}
