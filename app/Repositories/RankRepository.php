<?php

namespace App\Repositories;

use App\Models\Rank;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

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
