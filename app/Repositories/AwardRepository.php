<?php

namespace App\Repositories;

use App\Models\Award;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class AwardRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'title' => 'like',
    ];

    public function model()
    {
        return Award::class;
    }

    public function findByTitle($title) {
        return $this->findByField('title', $title)->first();
    }
}
