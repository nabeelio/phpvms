<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Repositories\PirepRepository;

/**
 * Show the latest PIREPs in a view
 * @package App\Widgets
 */
class LatestPireps extends Widget
{
    protected $config = [
        'count' => 5,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $pirepRepo = app(PirepRepository::class);

        return view('widgets.latest_pireps', [
            'config' => $this->config,
            'pireps' => $pirepRepo->recent($this->config['count']),
        ]);
    }
}
