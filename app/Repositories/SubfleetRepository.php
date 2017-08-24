<?php

namespace App\Repositories;

use App\Models\Subfleet;

class SubfleetRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Subfleet::class;
    }
}
