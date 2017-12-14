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
    public function checkDbConnection($type, $host, $port, $name, $user, $pass)
    {
        Log::info('Testing Connection: '.$type.'::'.$user.':'.$pass.'@'.$host.':'.$port.';'.$name);

        if($type === 'mysql') {
            $dsn = "mysql:host=$host;port=$port;dbname=$name";
            Log::info('Connection string: '. $dsn);
            try {
                $conn = new PDO($dsn, $user, $pass);
            } catch (\PDOException $e) {
                throw $e;
            }
        }

        // Needs testing
        elseif ($type === 'postgres') {
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
        \Artisan::call('database:create');
        \Artisan::call('migrate:refresh');
    }
}
