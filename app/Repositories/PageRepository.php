<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Page;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class PageRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * @return string
     */
    public function model()
    {
        return Page::class;
    }
}
