<?php

namespace App\Repositories;

use App\Models\Aircraft;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class AircraftRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * @var array
     */
    protected $fieldSearchable
        = [
            'name',
            'registration',
            'active',
        ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Aircraft::class;
    }
}
