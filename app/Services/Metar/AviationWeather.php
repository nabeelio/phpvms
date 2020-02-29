<?php

namespace App\Services\Metar;

use App\Contracts\Metar;
use App\Support\HttpClient;
use Exception;
use Illuminate\Support\Facades\Log;
use function count;

/**
 * Return the raw METAR string from the NOAA Aviation Weather Service
 */
class AviationWeather extends Metar
{
    private const METAR_URL =
        'https://www.aviationweather.gov/adds/dataserver_current/httpparam?'
        .'dataSource=metars&requestType=retrieve&format=xml&hoursBeforeNow=3'
        .'&mostRecent=true&fields=raw_text&stationString=';

    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Implement the METAR - Return the string
     *
     * @param $icao
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    protected function metar($icao): string
    {
        if ($icao === '') {
            return '';
        }

        $url = static::METAR_URL.$icao;

        try {
            $res = $this->httpClient->get($url, []);
            $xml = simplexml_load_string($res);

            $attrs = $xml->data->attributes();
            if (!isset($attrs['num_results'])) {
                return '';
            }

            $num_results = $attrs['num_results'];
            if (empty($num_results)) {
                return '';
            }

            $num_results = intval($num_results);
            if ($num_results === 0) {
                return '';
            }

            if (count($xml->data->METAR->raw_text) === 0) {
                return '';
            }

            return $xml->data->METAR->raw_text->__toString();
        } catch (Exception $e) {
            Log::error('Error reading METAR: '.$e->getMessage());

            return '';
        }
    }
}
