<?php

namespace App\Services;

use App\Contracts\Service;
use App\Events\NewsAdded;
use App\Repositories\NewsRepository;

class NewsService extends Service
{
    private NewsRepository $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    /**
     * Add a news item
     *
     * @param array $attrs
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function addNews(array $attrs)
    {
        $news = $this->newsRepo->create($attrs);
        event(new NewsAdded($news));

        return $news;
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
