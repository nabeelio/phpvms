<?php

namespace App\Repositories;

use App\Models\Airline;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirlineRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

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
        return Airline::class;
    }
}
