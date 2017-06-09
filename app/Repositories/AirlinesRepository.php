<?php

namespace App\Repositories;

use App\Models\Airlines;
use InfyOm\Generator\Common\BaseRepository;

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
