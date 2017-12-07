<?php

namespace App\Repositories;

use App\Models\Subfleet;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

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
