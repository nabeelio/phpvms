<?php

namespace App\Repositories;

use App\Models\Subfleet;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class SubfleetRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'type' => 'like',
    ];

    public function model()
    {
        return Subfleet::class;
    }
}
