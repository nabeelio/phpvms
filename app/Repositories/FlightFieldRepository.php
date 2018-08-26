<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\FlightField;

/**
 * Class FlightFieldRepository
 */
class FlightFieldRepository extends Repository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    /**
     * @return string
     */
    public function model(): string
    {
        return FlightField::class;
    }
}
