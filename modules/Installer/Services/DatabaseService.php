<?php

namespace Modules\Installer\Services;

use App\Interfaces\Service;
use Log;
use PDO;

class DatabaseService extends Service
{
    /**
     * Check the PHP version that it meets the minimum requirement
     * @param $driver
     * @param $host
     * @param $port
     * @param $name
     * @param $user
     * @param $pass
     * @return boolean
     */
    public function checkDbConnection($driver, $host, $port, $name, $user, $pass)
    {
        Log::info('Testing Connection: '.$driver.'::'.$user.':<hidden>@'.$host.':'.$port.';'.$name);

        if($driver === 'mysql') {
            $dsn = "mysql:host=$host;port=$port;dbname=$name";
            Log::info('Connection string: '. $dsn);
            try {
                $conn = new PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                throw $e;
            }
        }

        // TODO: Needs testing
        elseif ($driver === 'postgres') {
            $dsn = "pgsql:host=$host;port=$port;dbname=$name";
            try {
                $conn = new PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Setup the database by running the migration commands
     * Only run the setup for sqlite, otherwise, we're assuming
     * that the MySQL database has already been created
     */
    public function setupDB()
    {
        $output = '';

        if(config('database.default') === 'sqlite') {
            \Artisan::call('database:create');
            $output .= \Artisan::output();
        }

        return trim($output);
    }
}
