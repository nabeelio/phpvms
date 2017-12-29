<?php

namespace Modules\Installer\Services;

use Log;
use PDO;

class DatabaseService {

    /**
     * Check the PHP version that it meets the minimum requirement
     * @throws \PDOException
     * @return boolean
     */
    public function checkDbConnection($driver, $host, $port, $name, $user, $pass)
    {
        Log::info('Testing Connection: '.$driver.'::'.$user.':'.$pass.'@'.$host.':'.$port.';'.$name);

        if($driver === 'mysql') {
            $dsn = "mysql:host=$host;port=$port;";
            Log::info('Connection string: '. $dsn);
            try {
                $conn = new PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                throw $e;
            }
        }

        // Needs testing
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
     */
    public function setupDB()
    {
        $output = '';

        \Artisan::call('database:create');
        $output .= \Artisan::output();

        \Artisan::call('migrate');
        $output .= trim(\Artisan::output());

        \Artisan::call('db:seed');
        $output .= trim(\Artisan::output());

        return trim($output);
    }
}
