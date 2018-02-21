<?php

namespace App\Widgets;

use App\Repositories\AcarsRepository;
use App\Services\GeoService;

/**
 * Show the live map in a view
 * @package App\Widgets
 */
class LiveMap extends BaseWidget
{
    protected $config = [
        'height' => '800px',
        'width' => '100%',
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $geoSvc = app(GeoService::class);
        $acarsRepo = app(AcarsRepository::class);

        $pireps = $acarsRepo->getPositions();
        $positions = $geoSvc->getFeatureForLiveFlights($pireps);

        return $this->view('widgets.live_map', [
            'config' => $this->config,
            'pireps' => $pireps,
            'positions' => $positions,
        ]);
    }
}
