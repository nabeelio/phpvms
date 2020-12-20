<?php

namespace App\Widgets;

use App\Contracts\Widget;
use App\Services\AirportService;
use App\Services\Metar\AviationWeather;

/**
 * This is a widget for the 3rd party CheckWX service
 */
class Weather extends Widget
{
    protected $config = ['icao' => null];

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        /** @var \App\Services\AirportService $airportSvc */
        $addsSvc = app(AviationWeather::class);
        $airportSvc = app(AirportService::class);
        $metar = $airportSvc->getMetar($this->config['icao']);
        $taf = $addsSvc->taf($this->config['icao']);

        return view('widgets.weather', [
            'config'    => $this->config,
            'icao'      => $this->config['icao'],
            'metar'     => $metar,
            'taf'       => $taf,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
