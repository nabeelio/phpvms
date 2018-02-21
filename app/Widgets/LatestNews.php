<?php

namespace App\Widgets;

use App\Repositories\NewsRepository;

/**
 * Show the latest news in a view
 * @package App\Widgets
 */
class LatestNews extends BaseWidget
{
    protected $config = [
        'count' => 5,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $newsRepo = app(NewsRepository::class);

        return $this->view('widgets.latest_news', [
            'config' => $this->config,
            'news' => $newsRepo->recent($this->config['count']),
        ]);
    }
}
