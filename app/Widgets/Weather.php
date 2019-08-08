<?php

namespace App\Widgets;

use App\Contracts\Widget;
use App\Services\AirportService;

/**
 * This is a widget for the 3rd party CheckWX service
 */
class Weather extends Widget
{
    protected $config = [
        'icao' => null,
    ];

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        $airportSvc = app(AirportService::class);
        $metar = $airportSvc->getMetar($this->config['icao']);

        return view('widgets.weather', [
            'config'    => $this->config,
            'metar'     => $metar,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
