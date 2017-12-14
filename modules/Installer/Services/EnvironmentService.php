<?php

namespace Modules\Installer\Services;

use Illuminate\Encryption\Encrypter;
use Log;

class EnvironmentService
{
    /**
     * Create the .env file
     * @return boolean
     */
    public function createEnvFile($type, $host, $port, $name, $user, $pass)
    {
        $opts = [
            'APP_KEY' => $this->createAppKey(),
            'DB_CONN' => $type,
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_NAME' => $name,
            'DB_USER' => $user,
            'DB_PASS' => $pass,
        ];

        $opts = $this->getCacheDriver($opts);
        $opts = $this->getQueueDriver($opts);

        $this->writeEnvFile($opts);

        return true;
    }

    /**
     * Generate a fresh new APP_KEY
     * @return string
     */
    protected function createAppKey()
    {
        return base64_encode(Encrypter::generateKey(config('app.cipher')));
    }

    /**
     * Determine is APC is installed, if so, then use it as a cache driver
     * @param $opts
     * @return mixed
     */
    protected function getCacheDriver($opts)
    {
        if(\extension_loaded('apc')) {
            $opts['CACHE_DRIVER'] = 'apc';
        } else {
            $opts['CACHE_DRIVER'] = 'filesystem';
        }

        return $opts;
    }

    /**
     * Setup a queue driver that's not the default "sync"
     * driver, if a database is being used
     * @param $opts
     * @return mixed
     */
    protected function getQueueDriver($opts)
    {
        # If we're setting up a database, then also setup
        # the default queue driver to use the database
        if ($opts['DB_CONN'] === 'mysql' || $opts['DB_CONN'] === 'postgres') {
            $opts['QUEUE_DRIVER'] = 'database';
        } else {
            $opts['QUEUE_DRIVER'] = 'sync';
        }

        return $opts;
    }

    /**
     * Get the template file name and write it out
     * @param $opts
     */
    protected function writeEnvFile($opts)
    {
        $app = app();
        $env_file = $app->environmentFilePath();
        $env_file .= config('installer.env_postfix');

        # render it within Blade and log the contents
        $env_contents = view('installer::stubs/env', $opts);
        Log::info($env_contents);

        $fp = fopen($env_file, 'w');
        fwrite($fp, $env_contents);
        fclose($fp);
    }
}
