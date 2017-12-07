<?php

namespace App\Repositories;

use App\Models\Fare;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class FareRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'code' => 'like',
        'name' => 'like',
        'notes' => 'like',
    ];

    public function model()
    {
        return Fare::class;
    }

    public function findByCode($code) {
        return $this->findByField('code', $code)->first();
    }
}
