<?php

namespace App\Services\Metar;

use App\Interfaces\Metar;
use App\Support\Http;
use Cache;

/**
 * Return the raw METAR string from the NOAA Aviation Weather Service
 */
class AviationWeather extends Metar
{
    private const METAR_URL =
        'https://www.aviationweather.gov/adds/dataserver_current/httpparam?'
        .'dataSource=metars&requestType=retrieve&format=xml&hoursBeforeNow=3'
        .'&mostRecent=true&fields=raw_text&stationString=';

    /**
     * Implement the METAR - Return the string
     *
     * @param $icao
     *
     * @return string
     */
    protected function metar($icao): string
    {
        $metar = Cache::remember(
            config('cache.keys.WEATHER_LOOKUP.key').$icao,
            config('cache.keys.WEATHER_LOOKUP.time'),
            function () use ($icao) {
                $url = static::METAR_URL.$icao;

                try {
                    $res = Http::get($url, []);
                    $xml = simplexml_load_string($res);
                    if (count($xml->data->METAR->raw_text) == 0) {
                        return '';
                    }
                    return $xml->data->METAR->raw_text->__toString();
                } catch (\Exception $e) {
                    return '';
                }
            }
        );

        return $metar;
    }
}
