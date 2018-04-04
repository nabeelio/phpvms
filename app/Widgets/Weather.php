<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Support\Http;
use App\Support\Metar;
use App\Support\Units\Distance;
use App\Support\Units\Temperature;
use Illuminate\Support\Facades\Cache;
use MetarDecoder\MetarDecoder;
use SimpleXMLElement;

/**
 * This is a widget for the 3rd party CheckWX service
 * @package App\Widgets
 */
class Weather extends Widget
{
    protected $config = [
        'icao' => null,
    ];

    public const URL = 'https://avwx.rest/api/metar/';

    /**
     * Determine the category depending on the rules for visibility/ceiling
     * https://www.aviationweather.gov/cva/help
     * @param $metar
     * @return string
     */
    protected function determineCategory($metar): string
    {

    }

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        /**
         * @var \App\Interfaces\Metar
         */
        $klass = config('phpvms.metar');
        $metar_class = new $klass;

        $metar = null;
        $wind = null;
        $raw_metar = $metar_class->get_metar($this->config['icao']);

        if ($raw_metar && $raw_metar !== '') {
            $metar = new Metar($raw_metar);
            $wind = $metar->getWinds();
        }

        return view('widgets.weather', [
            'config'    => $this->config,
            'metar'     => $metar,
            'wind'      => $wind,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
