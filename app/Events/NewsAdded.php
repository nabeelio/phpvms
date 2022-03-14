<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\News;

class NewsAdded extends Event
{
    public News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
}
