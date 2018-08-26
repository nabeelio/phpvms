<?php

namespace App\Console\Services;

use PDO;

/**
 * Class Database
 */
class Database
{
    /**
     * Create the base connection DSN, optionally include the DB name
     *
     * @param      $host
     * @param      $port
     * @param null $name
     *
     * @return string
     */
    public function createDsn($host, $port, $name = null)
    {
        $conn = config('database.default');
        $dsn = "$conn:host=$host;port=$port";
        if (filled($name)) {
            $dsn .= ';dbname='.$name;
        }

        return $dsn;
    }

    /**
     * @param $dsn
     * @param $user
     * @param $pass
     *
     * @throws \PDOException
     *
     * @return PDO
     */
    public function createPDO($dsn, $user, $pass)
    {
        try {
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            throw $e;
        }

        return $conn;
    }
}
