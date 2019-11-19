<?php

namespace App\Contracts;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Base class for implementing retrieving METARs
 */
abstract class Metar
{
    /**
     * Implement retrieving the METAR - return the METAR string. Needs to be protected,
     * since this shouldn't be directly called. Call `get_metar($icao)` instead
     *
     * @param $icao
     *
     * @return mixed
     */
    abstract protected function metar($icao): string;

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

        if (Cache::has($key)) {
            $raw_metar = Cache::get($key);
            if ($raw_metar !== '') {
                return $raw_metar;
            }
        }

        try {
            $raw_metar = $this->metar($icao);
        } catch (\Exception $e) {
            Log::error('Error getting METAR: '.$e->getMessage(), $e->getTrace());
            return '';
        }

        if ($raw_metar !== '') {
            Cache::put($key, $raw_metar, $cache['time']);
        }

        return $raw_metar;
    }
}
