<?php

namespace App\Repositories;

use App\Models\News;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class NewsRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return News::class;
    }

    /**
     * Latest news items
     * @param int $count
     * @return mixed
     */
    public function getLatest($count=5)
    {
        return $this->orderBy('created_at', 'desc')
                    ->with(['user'])
                    ->paginate($count);
    }
}
