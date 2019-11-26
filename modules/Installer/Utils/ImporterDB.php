<?php

namespace Modules\Installer\Utils;

use Illuminate\Support\Facades\Log;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use PDO;
use PDOException;

/**
 * Real basic to interface with an importer
 */
class ImporterDB
{
    /**
     * @var PDO
     */
    private $conn;

    private $dsn;
    private $creds;
    private $batchSize;

    public function __construct($creds)
    {
        $this->creds = $creds;
        $this->dsn = 'mysql:'.implode(';', [
            'host='.$this->creds['host'],
            'port='.$this->creds['port'],
            'dbname='.$this->creds['name'],
            ]);

        Log::info('Using DSN: '.$this->dsn);

        $this->batchSize = config('installer.importer.batch_size');
    }

    public function connect()
    {
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
     * @param string $table The name of the table
     * @param int    [$start_offset]
     *
     * @throws \Modules\Installer\Exceptions\ImporterNextRecordSet
     * @throws \Modules\Installer\Exceptions\ImporterNoMoreRecords
     *
     * @return \Generator
     */
    public function readRows($table, $start_offset = 0)
    {
        $this->connect();

        $offset = $start_offset;
        $total_rows = $this->getTotalRows($table);

        while ($offset < $total_rows) {
            $rows_to_read = $offset + $this->batchSize;
            if ($rows_to_read > $total_rows) {
                $rows_to_read = $total_rows;
            }

            Log::info('Reading '.$offset.' to '.$rows_to_read.' of '.$total_rows);
            yield from $this->readRowsOffset($table, $this->batchSize, $offset);

            $offset += $this->batchSize;
        }
    }

    /**
     * @param string $table
     * @param int    $limit  Number of rows to read
     * @param int    $offset Where to start from
     *
     * @throws ImporterNextRecordSet
     * @throws ImporterNoMoreRecords
     *
     * @return \Generator
     */
    public function readRowsOffset($table, $limit, $offset)
    {
        $sql = 'SELECT * FROM '.$this->tableName($table).' LIMIT '.$limit.' OFFSET '.$offset;

        try {
            $result = $this->conn->query($sql);
            if (!$result) {
                throw new ImporterNoMoreRecords();
            }

            $rowCount = $result->rowCount();

            if (!$rowCount === 0) {
                throw new ImporterNoMoreRecords();
            }

            foreach ($result as $row) {
                yield $row;
            }

            // No more records left since we got the number below the limit
            if ($rowCount < $limit) {
                throw new ImporterNoMoreRecords();
            }

            throw new ImporterNextRecordSet($offset + $limit);
        } catch (PDOException $e) {
            // Without incrementing the offset, it should re-run the same query
            Log::error('Error readRowsOffset: '.$e->getMessage());

            if (strpos($e->getMessage(), 'server has gone away') !== false) {
                $this->connect();
            }
        }
    }
}
