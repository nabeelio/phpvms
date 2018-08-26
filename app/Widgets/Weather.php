<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Support\Metar;

/**
 * This is a widget for the 3rd party CheckWX service
 */
class Weather extends Widget
{
    protected $config = [
        'icao' => null,
    ];

    public const URL = 'https://avwx.rest/api/metar/';

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        /**
         * @var \App\Interfaces\Metar
         */
        $klass = config('phpvms.metar');
        $metar_class = new $klass();

        $metar = null;
        $wind = null;
        $raw_metar = $metar_class->get_metar($this->config['icao']);

        if ($raw_metar && $raw_metar !== '') {
            $metar = new Metar($raw_metar);
        }

        return view('widgets.weather', [
            'config'    => $this->config,
            'metar'     => $metar,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
