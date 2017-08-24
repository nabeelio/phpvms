<?php

namespace App\Repositories;

use App\Models\Fare;
use Prettus\Repository\Eloquent\BaseRepository;


class FareRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'name',
        'price',
        'cost',
        'notes',
        'active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Fare::class;
    }

    public function findByCode($code) {
        return $this->findByField('code', $code)->first();
    }
}
