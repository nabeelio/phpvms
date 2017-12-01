<?php

namespace App\Repositories;

use App\Models\Airline;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirlineRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'code',
        'name' => 'like',
    ];

    public function model()
    {
        return Airline::class;
    }
}
