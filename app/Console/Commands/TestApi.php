<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Console\BaseCommand;

class TestApi extends BaseCommand
{
    protected $signature = 'phpvms:test-api {apikey} {url}';

    protected $httpClient;

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $this->httpClient = new Client([
           'headers' => [
               'Authorization' => $this->argument('apikey'),
               'Content-type' => 'application/json',
               'X-API-Key' => $this->argument('apikey'),
           ]
        ]);

        $result = $this->httpClient->get($this->argument('url'));
        echo $result->getBody();
    }
}
