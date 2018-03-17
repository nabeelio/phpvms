<?php

namespace App\Repositories;

use App\Models\Award;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class AwardRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'title' => 'like',
    ];

    public function model(): string
    {
        return Award::class;
    }

    public function findByTitle($title) {
        return $this->findByField('title', $title)->first();
    }
}
