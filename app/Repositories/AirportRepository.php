<?php

namespace App\Repositories;

use App\Models\Airport;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirportRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'icao'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Airport::class;
    }

    public function create(array $attributes)
    {
        //$attributes['id'] = $attributes['icao'];
        return parent::create($attributes);
    }
}
