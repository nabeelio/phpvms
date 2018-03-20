<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Award;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class AwardRepository
 * @package App\Repositories
 */
class AwardRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'title' => 'like',
    ];

    public function model(): string
    {
        return Award::class;
    }

    public function findByTitle($title)
    {
        return $this->findByField('title', $title)->first();
    }
}
