<?php

namespace App\Repositories;

use App\Models\PirepField;
use InfyOm\Generator\Common\BaseRepository;

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
