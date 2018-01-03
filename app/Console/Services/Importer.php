<?php

namespace App\Console\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDO;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Database\QueryException;
use Symfony\Component\Console\Output\ConsoleOutput;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Rank;
use App\Models\Subfleet;

/**
 * Class Importer
 * TODO: Batch import
 * @package App\Console\Services
 */
class Importer
{
    /**
     * Hold references
     */
    protected $airlines = [];
    protected $aircraft = [];
    protected $ranks = [];

    /**
     * Hold the PDO connection to the old database
     * @var
     */
    private $conn;

    /**
     * @var array
     */
    private $creds = [];

    /**
     * Hold the instance of the console logger
     * @var
     */
    private $log;

    /**
     * CONSTANTS
     */
    const BATCH_READ_ROWS = 500;
    const SUBFLEET_NAME = 'Imported Aicraft';

    public function __construct($db_creds)
    {
        // Setup the logger
        $this->log = new ConsoleOutput();

        # The db credentials
        $this->creds = array_merge([
            'host' => 'localhost',
            'port' => 3306,
            'name' => '',
            'user' => '',
            'pass' => '',
        ], $db_creds);
    }

    /**
     * @return int|void
     */
    public function run()
    {
        $this->reconnect();

        # Import all the different parts
        $this->importRanks();
        $this->importAirlines();
        $this->importAircraft();
        $this->importAirports();

        $this->importUsers();
        $this->importFlights();
        $this->importPireps();

        # Finish up
        $this->recalculateRanks();
    }

    /**
     * Reconnect to the old phpVMS DB using PDO
     */
    protected function reconnect()
    {
        $dsn = 'mysql:' . implode(';', [
               'host=' . $this->creds['host'],
               'port=' . $this->creds['port'],
               'dbname=' . $this->creds['name']
            ]);

        $this->info('Connection string: ' . $dsn);

        try {
            $this->conn = new PDO($dsn, $this->creds['user'], $this->creds['pass']);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            $this->error($e);
            exit();
        }
    }

    /**
     * @param $message
     */
    protected function comment($message)
    {
        $this->log->writeln('<comment>' . $message . '</comment>');
    }

    /**
     * @param $message
     */
    protected function error($message) {
        $this->log->writeln('<error>' . $message . '</error>');
    }

    /**
     * @param string $message
     */
    protected function info($message)
    {
        if(\is_array($message)) {
            print_r($message);
        }
        else {
            $this->log->writeln('<info>'.$message.'</info>');
        }
    }

    /**
     * Return the table name with the prefix
     * @param $table
     * @return string
     */
    protected function tableName($table)
    {
        return 'phpvms_'.$table;
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    protected function saveModel($model)
    {
        try {
            $model->save();
            return true;
        } catch (QueryException $e) {
            #$this->error($e->getMessage());
            return false;
        }
    }

    /**
     * @param $table
     * @return mixed
     */
    protected function getTotalRows($table)
    {
        $table = $this->tableName($table);

        $sql = 'SELECT COUNT(*) FROM ' . $table;
        $rows = $this->conn->query($sql)->fetchColumn();

        $this->info('Found '.$rows.' rows in '.$table);
        return $rows;
    }

    /**
     * Read all the rows in a table, but read them in a batched manner
     * @param string $table The name of the table
     * @return \Generator
     */
    protected function readRows($table)
    {
        $offset = 0;
        $total_rows = $this->getTotalRows($table);

        while($offset < $total_rows)
        {
            $rows_to_read = $offset+self::BATCH_READ_ROWS;
            if($rows_to_read > $total_rows) {
                $rows_to_read = $total_rows;
            }

            $this->info('Reading '.$offset.' to '.$rows_to_read.' of '.$total_rows);

            $sql = 'SELECT * FROM ' . $this->tableName($table)
                 . ' LIMIT ' . self::BATCH_READ_ROWS . ' OFFSET ' . $offset;

            try {
                foreach ($this->conn->query($sql) as $row) {
                    yield $row;
                }
            } catch(PDOException $e) {
                // Without incrementing the offset, it should re-run the same query
                $this->error($e);

                if(strpos($e->getMessage(), 'server has gone away') !== false) {
                    $this->reconnect();
                    continue;
                }
            }

            $offset += self::BATCH_READ_ROWS;
        }
    }

    /**
     *
     * All the individual importers, done on a per-table basis
     * Some tables get saved locally for tables that use FK refs
     *
     */

    /**
     * Import all of the ranks
     */
    protected function importRanks()
    {
        $this->comment('--- RANK IMPORT ---');

        $count = 0;
        foreach ($this->readRows('ranks') as $row)
        {
            $this->ranks[$row->rankid] = $row;
            $this->ranks[$row->rank] = $row;

            $attrs = [
                'name' => $row->rank,
                'link' => $row->rankimage,
                'hours' => $row->minhours,
            ];

            if($this->saveModel(new Rank($attrs))) {
                ++$count;
            }
        }

        $this->info('Imported ' . $count . ' ranks');
    }

    /**
     * Import all of the airlines. Save them all in the private var $airlines
     * They're used to lookup from other reference tables
     */
    protected function importAirlines()
    {
        $this->comment('--- AIRLINE IMPORT ---');

        $count = 0;
        foreach ($this->readRows('airlines') as $row)
        {
            $this->airlines[$row->id] = $row;
            $this->airlines[$row->code] = $row;

            $attrs = [
                'icao' => $row->code,
                'iata' => $row->code,
                'name' => $row->name,
                'active' => $row->enabled,
            ];

            if($this->saveModel(new Airline($attrs))) {
                ++$count;
            }
        }

        $this->info('Imported '. $count.' airlines');
    }

    /**
     * Imported the aircraft
     */
    protected function importAircraft()
    {
        $this->comment('--- AIRCRAFT IMPORT ---');

        $subfleet = Subfleet::where('name', self::SUBFLEET_NAME)
                            ->first();

        if($subfleet === null) {
            $this->info('Subfleet not found, inserting');
            $this->saveModel(new Subfleet([
                'name' => self::SUBFLEET_NAME,
                'type' => 'PHPVMS'
            ]));
        }

        $this->info('Subfleet ID is '.$subfleet->id);

        $count = 0;
        foreach($this->readRows('aircraft') as $row)
        {
            $this->aircraft[$row->id] = $row;

            $attrs = [
                'subfleet_id' => $subfleet->id,
                'icao' => $row->icao,
                'name' => $row->fullname,
                'registration' => $row->registration,
                'active' => $row->enabled
            ];

            if ($this->saveModel(new Aircraft($attrs))) {
                ++$count;
            }
        }

        $this->info('Imported ' . $count . ' aircraft');
    }

    /**
     * Import all of the airports
     */
    protected function importAirports()
    {
        $this->comment('--- AIRPORT IMPORT ---');

        $count = 0;
        foreach ($this->readRows('airports') as $row)
        {
            $attrs = [
                'id' => $row->icao,
                'icao' => $row->icao,
                'name' => $row->name,
                'country' => $row->country,
                'lat' => $row->lat,
                'lon' => $row->lng,
                'hub' => $row->hub,
            ];

            if($this->saveModel(new Airport($attrs))) {
                ++$count;
            }
        }

        $this->info('Imported ' . $count . ' airports');
    }

    /**
     * Import the flights and schedules
     */
    protected function importFlights()
    {
        $this->comment('--- FLIGHT SCHEDULE IMPORT ---');

        $count = 0;
        foreach ($this->readRows('schedules') as $row)
        {

        }

        $this->info('Imported ' . $count . ' flights');
    }

    /**
     * Import all of the PIREPs
     */
    protected function importPireps()
    {
        $this->comment('--- PIREP IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pireps') as $row)
        {

        }

        $this->info('Imported ' . $count . ' pireps');
    }

    protected function importUsers()
    {
        $this->comment('--- USER IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pilots') as $row)
        {

        }

        $this->info('Imported ' . $count . ' users');
    }

    /**
     * Recalculate all of the user ranks
     */
    protected function recalculateRanks()
    {
        $this->comment('--- RECALCULATING RANKS ---');
    }
}
