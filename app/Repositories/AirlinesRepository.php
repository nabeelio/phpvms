<?php

namespace App\Repositories;

use App\Models\Airlines;

class AirlinesRepository extends BaseRepository
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
        return Airlines::class;
    }
}
