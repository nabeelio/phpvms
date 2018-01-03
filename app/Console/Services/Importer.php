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
use App\Models\User;
use App\Models\Enums\UserState;
use App\Facades\Utils;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

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
    const SUBFLEET_NAME = 'Imported Aircraft';

    /**
     * Importer constructor.
     * @param $db_creds
     */
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
            'table_prefix' => 'phpvms_'
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
        if($this->creds['table_prefix'] !== false) {
            return $this->creds['table_prefix'].$table;
        }

        return $table;
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
            # return false;
            return $this->error($e->getMessage());
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
        return (int) $rows;
    }

    /**
     * Read all the rows in a table, but read them in a batched manner
     * @param string $table The name of the table
     * @return \Generator
     */
    protected function readRows($table)
    {
        // Set the table prefix if it has been entered
        $this->tableName($table);

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
     * Return the subfleet
     * @return mixed
     */
    protected function getSubfleet()
    {
        $subfleet = Subfleet::where('name', self::SUBFLEET_NAME)
            ->first();

        if ($subfleet === null)
        {
            $this->info('Subfleet not found, inserting');
            $airline = Airline::first();
            $subfleet = new Subfleet([
                'airline_id' => $airline->id,
                'name' => self::SUBFLEET_NAME,
                'type' => 'PHPVMS'
            ]);

            $this->saveModel($subfleet);
        }

        return $subfleet;
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
                'image_link' => $row->rankimage,
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

        $subfleet = $this->getSubfleet();

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
        /*$this->comment('--- FLIGHT SCHEDULE IMPORT ---');

        $count = 0;
        foreach ($this->readRows('schedules') as $row)
        {

        }

        $this->info('Imported ' . $count . ' flights');*/
    }

    /**
     * Import all of the PIREPs
     */
    protected function importPireps()
    {
        /*$this->comment('--- PIREP IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pireps') as $row)
        {

        }

        $this->info('Imported ' . $count . ' pireps');*/
    }

    protected function importUsers()
    {
        $this->comment('--- USER IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pilots') as $row)
        {
            # TODO: What to do about pilot ids

            $name = $row->firstname.' '.$row->lastname;
            $airline_id = $this->airlines[$row->code];
            $rank_id = $this->ranks[$row->rank];
            $state = $this->getUserState($row->retired);

            $attrs = [
                'name' => $name,
                'email' => $row->email,
                'password' => "",
                'api_key' => "",
                'airline_id' => $airline_id->id,
                'rank_id' => $rank_id->rankid,
                'home_airport_id' => $row->hub,
                'curr_airport_id' => $row->hub,
                'flights' => (int) $row->totalflights,
                'flight_time' => Utils::hoursToMinutes($row->totalhours),
                'state' => $state,
            ];

            if($this->saveModel(new User($attrs))) {
                ++$count;
            }
        }

        $this->info('Imported ' . $count . ' users');

        // Reset Passwords & Generate API Keys
        $this->setupUsers();
    }

    /**
     * Recalculate all of the user ranks
     */
    protected function recalculateRanks()
    {
        /*$this->comment('--- RECALCULATING RANKS ---');*/
    }

    /**
     * Generate user's API Key and email them their new password
     */
    protected function setupUsers()
    {
        $allusers = User::all();
        foreach($allusers as $user)
        {
            # Generate New User Password
            $newpw = substr(md5(date('mdYhs')), 0, 10);

            # Generate API Key
            $api_key = Utils::generateApiKey();

            # Update Info in DB
            $user->password = Hash::make($newpw);
            $user->api_key = $api_key;
            $user->save();
        }

        # TODO: Think about how to deliver new password to user, email in batch at the end?
        # TODO: How to reset password upon first login only for reset users
    }

    /**
     * Get the user's new state from their original state
     */
    protected function getUserState($state)
    {
        // Declare array of classic states
        $phpvms_classic_states = [
            'ACTIVE' => 0,
            'INACTIVE' => 1,
            'BANNED' => 2,
            'ON_LEAVE' => 3
        ];

        // Decide which state they will be in accordance with v7
        if ($state == $phpvms_classic_states['ACTIVE'])
        {
            # Active
            return UserState::ACTIVE;
        } elseif ($state == $phpvms_classic_states['INACTIVE']) {
            # Rejected
            # TODO: Make an inactive state?
            return UserState::REJECTED;
        } elseif ($state == $phpvms_classic_states['BANNED']) {
            # Suspended
            return UserState::SUSPENDED;
        } elseif ($state == $phpvms_classic_states['ON_LEAVE']) {
            # On Leave
            return UserState::ON_LEAVE;
        }
    }
}
