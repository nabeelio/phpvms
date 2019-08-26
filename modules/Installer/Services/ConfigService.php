<?php

namespace Modules\Installer\Services;

use App\Contracts\Service;
use Exception;
use function extension_loaded;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use function is_bool;
use Nwidart\Modules\Support\Stub;
use PDO;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class ConfigService
 */
class ConfigService extends Service
{
    /**
     * Create the .env file
     *
     * @param $attrs
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     *
     * @return bool
     */
    public function createConfigFiles($attrs): bool
    {
        $opts = [
            'APP_ENV'             => 'dev',
            'APP_KEY'             => $this->createAppKey(),
            'SITE_NAME'           => '',
            'SITE_URL'            => 'http://phpvms.test',
            'CACHE_PREFIX'        => '',
            'DB_CONN'             => '',
            'DB_HOST'             => '',
            'DB_PORT'             => 3306,
            'DB_NAME'             => '',
            'DB_USER'             => '',
            'DB_PASS'             => '',
            'DB_PREFIX'           => '',
            'DB_EMULATE_PREPARES' => false,
        ];

        $opts = array_merge($opts, $attrs);

        $opts = $this->determinePdoOptions($opts);
        $opts = $this->configCacheDriver($opts);
        $opts = $this->configQueueDriver($opts);

        $this->writeConfigFiles($opts);

        return true;
    }

    /**
     * Update the environment file and update certain keys/values
     *
     * @param array $kvp
     *
     * @return void
     */
    public function updateKeysInEnv(array $kvp)
    {
        $app = app();

        $env_file = file_get_contents($app->environmentFilePath());
        foreach ($kvp as $key => $value) {
            $key = strtoupper($key);

            // cast for any boolean values
            if (is_bool($value)) {
                $value = $value === true ? 'true' : 'false';
            }

            // surround by quotes if there are any spaces in the value
            if (strpos($value, ' ') !== false) {
                $value = '"'.$value.'"';
            }

            Log::info('Replacing "'.$key.'" with '.$value);

            $env_file = preg_replace(
                '/^'.$key.'(.*)?/m',
                $key.'='.$value,
                $env_file
            );
        }

        file_put_contents($app->environmentFilePath(), $env_file);
    }

    /**
     * Generate a fresh new APP_KEY
     *
     * @return string
     */
    protected function createAppKey(): string
    {
        return base64_encode(Encrypter::generateKey(config('app.cipher')));
    }

    /**
     * Change a few options within the PDO driver, depending on the version
     * of mysql/maria, etc used. ATM, only make a change for MariaDB
     *
     * @param $opts
     *
     * @return mixed
     */
    protected function determinePdoOptions($opts)
    {
        if ($opts['DB_CONN'] !== 'mysql') {
            return $opts;
        }

        $dsn = "mysql:host=$opts[DB_HOST];port=$opts[DB_PORT];";
        Log::info('Connection string: '.$dsn);

        $conn = new PDO($dsn, $opts['DB_USER'], $opts['DB_PASS']);
        $version = strtolower($conn->getAttribute(PDO::ATTR_SERVER_VERSION));
        Log::info('Detected DB Version: '.$version);

        // If it's mariadb, enable the emulation for prepared statements
        // seems to be throwing a problem on 000webhost
        // https://github.com/nabeelio/phpvms/issues/132
        if (strpos($version, 'mariadb') !== false) {
            Log::info('Detected MariaDB, setting DB_EMULATE_PREPARES to true');
            $opts['DB_EMULATE_PREPARES'] = true;
        }

        return $opts;
    }

    /**
     * Determine is APC is installed, if so, then use it as a cache driver
     *
     * @param $opts
     *
     * @return mixed
     */
    protected function configCacheDriver($opts)
    {
        // Set the cache prefix
        $opts['CACHE_PREFIX'] = $opts['SITE_NAME'].'_';

        // Figure out what cache driver to initially use, depending on
        // what is installed. It won't detect redis or anything, though
        foreach (config('installer.cache.drivers') as $ext => $driver) {
            if (extension_loaded($ext)) {
                Log::info('Detected extension "'.$ext.'", setting driver to "'.$driver.'"');
                $opts['CACHE_DRIVER'] = $driver;
                return $opts;
            }
        }

        Log::info('No extension detected, using file cache');
        $opts['CACHE_DRIVER'] = config('installer.cache.default');
        return $opts;
    }

    /**
     * Setup a queue driver that's not the default "sync"
     * driver, if a database is being used
     *
     * @param $opts
     *
     * @return mixed
     */
    protected function configQueueDriver($opts)
    {
        // If we're setting up a database, then also setup
        // the default queue driver to use the database
        if ($opts['DB_CONN'] === 'mysql' || $opts['DB_CONN'] === 'postgres') {
            $opts['QUEUE_DRIVER'] = 'database';
        } else {
            $opts['QUEUE_DRIVER'] = 'sync';
        }

        return $opts;
    }

    /**
     * Remove the config files
     */
    public function removeConfigFiles()
    {
        $env_file = App::environmentFilePath();
        $config_file = App::environmentPath().'/config.php';

        if (file_exists($env_file)) {
            try {
                unlink($env_file);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        if (file_exists($config_file)) {
            try {
                unlink($config_file);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    /**
     * Get the template file name and write it out
     *
     * @param $opts
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function writeConfigFiles($opts)
    {
        Stub::setBasePath(resource_path('/stubs/installer'));

        $env_file = App::environmentFilePath();

        if (file_exists($env_file) && !is_writable($env_file)) {
            Log::error('Permissions on existing env.php is not writable');

            throw new FileException('Can\'t write to the env.php file! Check the permissions');
        }

        /*
         * First write out the env file
         */
        try {
            $stub = new Stub('/env.stub', $opts);
            $stub->render();
            $stub->saveTo(App::environmentPath(), App::environmentFile());
        } catch (Exception $e) {
            throw new FileException('Couldn\'t write env.php. ('.$e.')');
        }

        /*
         * Next write out the config file. If there's an error here,
         * then throw an exception but delete the env file first
         */
        try {
            $stub = new Stub('/config.stub', $opts);
            $stub->render();
            $stub->saveTo(App::environmentPath(), 'config.php');
        } catch (Exception $e) {
            unlink(App::environmentPath().'/'.App::environmentFile());

            throw new FileException('Couldn\'t write config.php. ('.$e.')');
        }
    }
}
