<?php

namespace App\Support;

use GuzzleHttp\Client;

/**
 * Helper for HTTP stuff
 * @package App\Support
 */
class Http
{
    /**
     * Download a URI. If a file is given, it will save the downloaded
     * content into that file
     * @param       $uri
     * @param array $opts
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function get($uri, array $opts)
    {
        $opts = array_merge([
            'connect_timeout' => 2, // wait two seconds by default
        ], $opts);

        $client = new Client();
        $response = $client->request('GET', $uri, $opts);

        $body = $response->getBody()->getContents();
        if ($response->getHeader('content-type') === 'application/json') {
            $body = \GuzzleHttp\json_decode($body);
        }

        return $body;
    }
}
