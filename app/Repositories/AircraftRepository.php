<?php

namespace App\Repositories;

use App\Models\Aircraft;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class AircraftRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'registration' => 'like',
        'active',
    ];

    public function model()
    {
        return Aircraft::class;
    }
}
