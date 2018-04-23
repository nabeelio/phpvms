<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Repositories\UserRepository;

/**
 * Show the latest pilots in a view
 * @package App\Widgets
 */
class LatestPilots extends Widget
{
    protected $config = [
        'count' => 5,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $userRepo = app(UserRepository::class);

        return view('widgets.latest_pilots', [
            'config' => $this->config,
            'users'  => $userRepo->recent($this->config['count']),
        ]);
    }
}
