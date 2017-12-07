<?php

namespace App\Repositories;

use App\Models\Rank;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class RankRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
    ];

    public function model()
    {
        return Rank::class;
    }
}
