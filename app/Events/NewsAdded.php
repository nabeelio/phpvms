<?php

namespace App\Events;

use App\Models\News;

class NewsAdded extends BaseEvent
{
    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
}
