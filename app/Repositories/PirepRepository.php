<?php

namespace App\Repositories;

use App\Models\Pirep;

class PirepRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pirep::class;
    }
}
