<?php

namespace App\Widgets;

use App\Contracts\Widget;
use App\Repositories\UserRepository;

/**
 * Show the latest pilots in a view
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
            'users'  => $userRepo->with(['airline'])->recent($this->config['count']),
        ]);
    }
}
