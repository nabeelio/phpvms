<?php

namespace Modules\Installer\Utils;

use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

/**
 * Real basic to interface with an importer
 */
class ImporterDB
{
    public const BATCH_READ_ROWS = 300;
    private $conn;
    private $dsn;
    private $creds;

    public function __construct($creds)
    {
        $this->creds = $creds;
        $this->dsn = 'mysql:'.implode(';', [
            'host='.$this->creds['host'],
            'port='.$this->creds['port'],
            'dbname='.$this->creds['name'],
            ]);

        Log::info('Using DSN: '.$this->dsn);
    }

    public function connect()
    {
        Log::info('Connection string: '.$this->dsn);

        try {
            $this->conn = new PDO($this->dsn, $this->creds['user'], $this->creds['pass']);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            Log::error($e);
            exit();
        }
    }

    /**
     * Return the table name with the prefix
     *
     * @param $table
     *
     * @return string
     */
    public function tableName($table)
    {
        if ($this->creds['table_prefix'] !== false) {
            return $this->creds['table_prefix'].$table;
        }

        return $table;
    }

    /**
     * @param $table
     *
     * @return mixed
     */
    public function getTotalRows($table)
    {
        $this->connect();

        $sql = 'SELECT COUNT(*) FROM '.$this->tableName($table);
        $rows = $this->conn->query($sql)->fetchColumn();

        Log::info('Found '.$rows.' rows in '.$table);

        return (int) $rows;
    }

    /**
     * Read all the rows in a table, but read them in a batched manner
     *
     * @param string $table     The name of the table
     * @param null   $read_rows Number of rows to read
     *
     * @return \Generator
     */
    public function readRows($table, $read_rows = null)
    {
        $this->connect();

        $offset = 0;
        if ($read_rows === null) {
            $read_rows = self::BATCH_READ_ROWS;
        }

        $total_rows = $this->getTotalRows($table);

        while ($offset < $total_rows) {
            $rows_to_read = $offset + $read_rows;
            if ($rows_to_read > $total_rows) {
                $rows_to_read = $total_rows;
            }

            Log::info('Reading '.$offset.' to '.$rows_to_read.' of '.$total_rows);

            yield from $this->readRowsOffset($table, self::BATCH_READ_ROWS, $offset);

            $offset += self::BATCH_READ_ROWS;
        }
    }

    /**
     * @param string $table
     * @param int    $limit  Number of rows to read
     * @param int    $offset Where to start from
     *
     * @return \Generator
     */
    public function readRowsOffset($table, $limit, $offset)
    {
        $sql = 'SELECT * FROM '.$this->tableName($table).' LIMIT '.$limit.' OFFSET '.$offset;

        try {
            foreach ($this->conn->query($sql) as $row) {
                yield $row;
            }
        } catch (PDOException $e) {
            // Without incrementing the offset, it should re-run the same query
            Log::error($e);

            if (strpos($e->getMessage(), 'server has gone away') !== false) {
                $this->connect();
            }
        }
    }
}
