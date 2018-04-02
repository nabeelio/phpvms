<?php

namespace App\Widgets;

use App\Interfaces\Widget;
use App\Support\Http;
use Illuminate\Support\Facades\Cache;

/**
 * This is a widget for the 3rd party CheckWX service
 * @package App\Widgets
 */
class CheckWx extends Widget
{
    protected $config = [
        'icao' => null,
    ];

    /**
     * Attempt to get the data from the CheckWX API
     */
    public function run()
    {
        if (!config('checkwx.api_key')) {
            $data = null;
        } else {
            // Cache the request so we don't need to repeatedly call out
            $cache = config('cache.keys.WEATHER_LOOKUP');
            $key = $cache['key'].$this->config['icao'];

            $data = Cache::remember($key, $cache['time'], function () {
                $url = config('checkwx.url').'/metar/'.$this->config['icao'].'/decoded';
                $data = Http::get($url, [
                    'headers' => [
                        'X-API-Key'    => config('checkwx.api_key'),
                        'content-type' => 'application/json',
                    ]
                ]);

                $data = json_decode($data);
                return $data;
            });

            if($data->results === 1) {
                $data = $data->data[0];
            } else {
                $data = null;
            }
        }

        return view('widgets.check_wx', [
            'config'    => $this->config,
            'data'      => $data,
            'unit_alt'  => setting('units.altitude'),
            'unit_dist' => setting('units.distance'),
            'unit_temp' => setting('units.temperature'),
        ]);
    }
}
