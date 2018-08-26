<?php

namespace App\Interfaces;

use Cache;
use Log;

/**
 * Base class for implementing retrieving METARs
 */
abstract class Metar
{
    /**
     * Implement retrieving the METAR- Return the string
     * Needs to be protected, since this shouldn't be
     * directly called. Call `get_metar($icao)` instead
     *
     * @param $icao
     *
     * @return mixed
     */
    abstract protected function metar($icao): string;

    /**
     * @param $icao
     *
     * @return string
     */
    //abstract protected function taf($icao): string;

    /**
     * Download the METAR, wrap in caching
     *
     * @param $icao
     *
     * @return string
     */
    public function get_metar($icao): string
    {
        $cache = config('cache.keys.WEATHER_LOOKUP');
        $key = $cache['key'].$icao;

        $raw_metar = Cache::remember($key, $cache['time'], function () use ($icao) {
            try {
                return $this->metar($icao);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                Log::error('Error getting METAR: '.$e->getMessage(), $e->getTrace());
                return '';
            } catch (\Exception $e) {
                Log::error('Error getting METAR: '.$e->getMessage(), $e->getTrace());
                return '';
            }
        });

        return $raw_metar;
    }
}
