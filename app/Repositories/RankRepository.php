<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Rank;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class RankRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Rank::class;
    }
}
