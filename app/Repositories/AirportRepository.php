<?php

namespace App\Repositories;

use App\Models\Airport;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirportRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'icao',
        'name' => 'like',
    ];

    public function model()
    {
        return Airport::class;
    }
}
