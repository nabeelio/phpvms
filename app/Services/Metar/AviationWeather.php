<?php

namespace App\Services\Metar;

use App\Interfaces\Metar;
use App\Support\Http;

/**
 * Return the raw METAR string from the NOAA Aviation Weather Service
 * @package App\Services\Metar
 */
class AviationWeather extends Metar
{
    private const METAR_URL =
        'https://www.aviationweather.gov/adds/dataserver_current/httpparam?'
        .'dataSource=metars&requestType=retrieve&format=xml&hoursBeforeNow=3'
        .'&mostRecent=true&fields=raw_text&stationString=';

    /**
     * Implement the METAR - Return the string
     * @param $icao
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function metar($icao): string
    {
        $url = static::METAR_URL.$icao;
        $res = Http::get($url, []);
        $xml = simplexml_load_string($res);
        return $xml->data->METAR->raw_text->__toString();
    }
}
