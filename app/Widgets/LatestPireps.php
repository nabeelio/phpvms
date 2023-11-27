<?php

namespace App\Widgets;

use App\Contracts\Widget;
use App\Models\Enums\PirepState;
use App\Repositories\PirepRepository;

/**
 * Show the latest PIREPs in a view
 * sorted nicely by their submit time
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
            ->with(['airline', 'aircraft', 'user'])
            ->whereNotIn('state', [
                PirepState::DRAFT,
                PirepState::IN_PROGRESS,
                PirepState::CANCELLED,
            ])
            ->orderBy('submitted_at', 'desc')
            ->take($this->config['count'])
            ->get();

        return view('widgets.latest_pireps', [
            'config' => $this->config,
            'pireps' => $pireps,
        ]);
    }
}
