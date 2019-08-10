<?php

namespace App\Console\Commands;

use App\Console\Command;
use GuzzleHttp\Client;

/**
 * Class TestApi
 */
class TestApi extends Command
{
    protected $signature = 'phpvms:test-api {apikey} {url}';
    protected $httpClient;

    public function __construct()
    {
        parent::__construct();
        $this->redirectLoggingToFile('stdout');
    }

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $this->httpClient = new Client([
            'headers' => [
                'Authorization' => $this->argument('apikey'),
                'Content-type'  => 'application/json',
                'X-API-Key'     => $this->argument('apikey'),
            ],
        ]);

        $result = $this->httpClient->get($this->argument('url'));
        echo $result->getBody();
    }
}
