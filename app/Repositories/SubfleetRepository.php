<?php

namespace App\Repositories;

use App\Models\Subfleet;
use InfyOm\Generator\Common\BaseRepository;

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
