<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Support\Http;
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
        $category = 'VFR';
        $visibility = $metar->getVisibility()->getVisibility()->getValue();
        $ceiling = $metar->getClouds();
        if ($ceiling) {
            $ceiling = $ceiling[0]->getBaseHeight()->getValue();
        } else {
            $ceiling = 1000;
        }

        if ($visibility < 3 || $ceiling < 1000) {
            $category = 'IFR';
        }

        return $category;
    }

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        // Cache the request so we don't need to repeatedly call out
        $cache = config('cache.keys.WEATHER_LOOKUP');
        $key = $cache['key'].$this->config['icao'];

        $raw_metar = Cache::remember($key, $cache['time'], function () {
            /**
             * @var \App\Interfaces\Metar $klass
             */
            $klass = config('phpvms.metar');
            $metar_class = new $klass;
            return $metar_class->get($this->config['icao']);
        });

        // Run through this parser
        $decoder = new MetarDecoder();
        $metar = $decoder->parse($raw_metar);

        // Determine the flight category that's allowed
        // Just check if we need to be under IFR conditions
        $category = $this->determineCategory($metar);

        return view('widgets.weather', [
            'config'    => $this->config,
            'category'  => $category,
            'metar'     => $metar,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
