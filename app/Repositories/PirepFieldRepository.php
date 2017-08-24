<?php

namespace App\Repositories;

use App\Models\PirepField;

class PirepFieldRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PirepField::class;
    }
}
