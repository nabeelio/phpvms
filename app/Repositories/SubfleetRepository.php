<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Subfleet;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class SubfleetRepository
 */
class SubfleetRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'type' => 'like',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Subfleet::class;
    }
}
