<?php

namespace App\Repositories;

use App\Models\Rank;
use InfyOm\Generator\Common\BaseRepository;

class RankRepository extends BaseRepository
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
        return Rank::class;
    }
}
