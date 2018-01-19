<?php

namespace Modules\Installer\Services;

use Log;
use PDO;
use Illuminate\Encryption\Encrypter;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class EnvironmentService
 * @package Modules\Installer\Services
 */
class EnvironmentService
{
    /**
     * Create the .env file
     * @return boolean
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function createEnvFile($driver, $host, $port, $name, $user, $pass)
    {
        $opts = [
            'APP_ENV' => 'dev',
            'APP_KEY' => $this->createAppKey(),
            'DB_CONN' => $driver,
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_NAME' => $name,
            'DB_USER' => $user,
            'DB_PASS' => $pass,
            'DB_EMULATE_PREPARES' => false,
        ];

        $opts = $this->determinePdoOptions($opts);
        $opts = $this->getCacheDriver($opts);
        $opts = $this->getQueueDriver($opts);

        $this->writeEnvFile($opts);

        return true;
    }

    /**
     * Update the environment file and update certain keys/values
     * @param array $kvp
     * @return void
     */
    public function updateKeysInEnv(array $kvp)
    {
        $app = app();

        $env_file = file_get_contents($app->environmentFilePath());
        foreach($kvp as $key => $value) {

            # cast
            if(\is_bool($value)) {
                $value = $value === true ? 'true' : 'false';
            }

            $env_file = preg_replace(
                '/^' . $key . '(.*)?/m',
                $key . '=' . $value,
                $env_file
            );
        }

        file_put_contents($app->environmentFilePath(), $env_file);
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
     * Change a few options within the PDO driver, depending on the version
     * of mysql/maria, etc used. ATM, only make a change for MariaDB
     * @param $opts
     * @return mixed
     */
    protected function determinePdoOptions($opts)
    {
        if($opts['DB_CONN'] !== 'mysql') {
            return $opts;
        }

        $dsn = "mysql:host=$opts[DB_HOST];port=$opts[DB_PORT];";
        Log::info('Connection string: ' . $dsn);

        $conn = new PDO($dsn, $opts['DB_USER'], $opts['DB_PASS']);
        $version = strtolower($conn->getAttribute(PDO::ATTR_SERVER_VERSION));
        Log::info('Detected DB Version: '.$version);

        # If it's mariadb, enable the emulation for prepared statements
        # seems to be throwing a problem on 000webhost
        # https://github.com/nabeelio/phpvms/issues/132
        if(strpos($version, 'mariadb') !== false) {
            Log::info('Detected MariaDB, setting DB_EMULATE_PREPARES to true');
            $opts['DB_EMULATE_PREPARES'] = true;
        }

        return $opts;
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
            $opts['CACHE_DRIVER'] = 'array';
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
     * Update a key/value pair in the env file
     * @param $key
     * @param $value
     */
    public static function changeEnvironmentVariable($key, $value, $quoted=false)
    {
        $env_file = \App::environmentFilePath();

        if (\is_bool(env($key))) {
            $old = env($key) ? 'true' : 'false';
        }

        if($quoted) {
            $value = '"'.$value.'"';
        }

        if (file_exists($env_file)) {
            file_put_contents($env_file, str_replace(
                "$key=" . $old, "$key=" . $value, file_get_contents($env_file)
            ));
        }
    }

    /**
     * Get the template file name and write it out
     * @param $opts
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function writeEnvFile($opts)
    {
        $env_file = \App::environmentFilePath();

        if(file_exists($env_file) && !is_writable($env_file)) {
            Log::error('Permissions on existing env.php is not writable');
            throw new FileException('Can\'t write to the env.php file! Check the permissions');
        }

        $fp = fopen($env_file, 'wb');
        if($fp === false) {
            throw new FileException('Couldn\'t write the env.php. (' . error_get_last() .')');
        }

        # render it within Blade and log the contents
        $env_contents = view('installer::stubs/env', $opts);
        Log::info($env_contents);

        $env_contents = "<?php exit(); ?>\n\n"
                        .$env_contents;

        fwrite($fp, $env_contents);
        fclose($fp);
    }
}
