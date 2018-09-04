<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Models\Enums\PirepState;
use App\Repositories\PirepRepository;

/**
 * Show the latest PIREPs in a view
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

        $pireps = $pirepRepo
            ->with(['airline', 'aircraft'])
            ->whereNotInOrder('state', [
                PirepState::CANCELLED,
                PirepState::DRAFT,
                PirepState::IN_PROGRESS,
            ], 'created_at', 'desc')
            ->recent($this->config['count']);

        return view('widgets.latest_pireps', [
            'config' => $this->config,
            'pireps' => $pireps,
        ]);
    }
}
