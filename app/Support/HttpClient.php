<?php

namespace App\Support;

use GuzzleHttp\Client;

/**
 * Helper for HTTP stuff
 */
class HttpClient
{
    private $httpClient;

    public function __construct(
        Client $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Download a URI. If a file is given, it will save the downloaded
     * content into that file
     *
     * @param       $uri
     * @param array $opts
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    public function get($uri, array $opts = [])
    {
        $opts = array_merge([
            'connect_timeout' => 2, // wait two seconds by default
        ], $opts);

        $response = $this->httpClient->request('GET', $uri, $opts);

        $body = $response->getBody()->getContents();
        $content_type = $response->getHeaderLine('content-type');
        if (strpos($content_type, 'application/json') !== false) {
            $body = \GuzzleHttp\json_decode($body, true);
        }

        return $body;
    }
}
