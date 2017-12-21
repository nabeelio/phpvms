<?php

namespace App\Repositories;

use App\Models\Navdata;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class NavdataRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    // Super short lived cache for when the navdata stuff is re-imported
    protected $cacheMinutes = 5;

    public function model()
    {
        return Navdata::class;
    }
}
