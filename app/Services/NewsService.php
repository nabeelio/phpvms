<?php

namespace App\Services;

use App\Contracts\Service;
use App\Repositories\NewsRepository;

class NewsService extends Service
{
    private $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    /**
     * Add a news item
     *
     * @param array $attrs
     *
     * @return mixed
     */
    public function addNews(array $attrs)
    {
        return $this->newsRepo->create($attrs);
    }

    /**
     * Delete something from the news items
     *
     * @param int $id ID of the news row to delete
     */
    public function deleteNews($id)
    {
        $this->newsRepo->delete($id);
    }
}
