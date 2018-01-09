<?php

namespace App\Widgets;

use App\Repositories\UserRepository;

class RecentPilots extends BaseWidget
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

        return $this->view('widgets.recent_pilots', [
            'config' => $this->config,
            'users' => $userRepo->recent($this->config['count']),
        ]);
    }
}
