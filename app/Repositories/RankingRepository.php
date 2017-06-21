<?php

namespace App\Repositories;

use App\Models\Ranking;
use InfyOm\Generator\Common\BaseRepository;

class RankingRepository extends BaseRepository
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
        return Ranking::class;
    }
}
