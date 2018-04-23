<?php

namespace App\Console\Services;

use App\Facades\Utils;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\FlightType;
use App\Models\Enums\PirepSource;
use App\Models\Enums\UserState;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Rank;
use App\Models\Subfleet;
use App\Models\User;
use Carbon\Carbon;
use PDOException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PDO;
use Symfony\Component\Console\Output\ConsoleOutput;

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
    private $mappedEntities = [];

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

    const BATCH_READ_ROWS = 300;
    const SUBFLEET_NAME   = 'Imported Aircraft';

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
            'host'         => '127.0.0.1',
            'port'         => 3306,
            'name'         => '',
            'user'         => '',
            'pass'         => '',
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
        $this->findLastPireps();
        $this->recalculateRanks();
    }

    /**
     * Reconnect to the old phpVMS DB using PDO
     */
    protected function reconnect()
    {
        $dsn = 'mysql:'.implode(';', [
                'host='.$this->creds['host'],
                'port='.$this->creds['port'],
                'dbname='.$this->creds['name']
            ]);

        $this->info('Connection string: '.$dsn);

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
        $this->log->writeln('<comment>'.$message.'</comment>');
    }

    /**
     * @param $message
     */
    protected function error($message)
    {
        $this->log->writeln('<error>'.$message.'</error>');
    }

    /**
     * @param string $message
     */
    protected function info($message)
    {
        if (\is_array($message)) {
            print_r($message);
        } else {
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
        if ($this->creds['table_prefix'] !== false) {
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
            if ($e->getCode() !== '23000') {
                $this->error($e);
            }

            return false;
        }
    }

    /**
     * Create a new mapping between an old ID and the new one
     * @param $entity
     * @param $old_id
     * @param $new_id
     */
    protected function addMapping($entity, $old_id, $new_id)
    {
        if (!array_key_exists($entity, $this->mappedEntities)) {
            $this->mappedEntities[$entity] = [];
        }

        $this->mappedEntities[$entity][$old_id] = $new_id;
    }

    /**
     * Return the ID for a mapping
     * @param $entity
     * @param $old_id
     * @return bool
     */
    protected function getMapping($entity, $old_id)
    {
        if (!array_key_exists($entity, $this->mappedEntities)) {
            return 0;
        }

        $entity = $this->mappedEntities[$entity];
        if (array_key_exists($old_id, $entity)) {
            return $entity[$old_id];
        }

        return 0;
    }

    /**
     * @param $date
     * @return Carbon
     */
    protected function parseDate($date)
    {
        $carbon = Carbon::parse($date);

        return $carbon;
    }

    /**
     * Take a decimal duration and convert it to minutes
     * @param $duration
     * @return float|int
     */
    protected function convertDuration($duration)
    {
        if (strpos($duration, '.') !== false) {
            $delim = '.';
        } elseif (strpos($duration, ':')) {
            $delim = ':';
        } else {
            # no delimiter, assume it's just a straight hour
            return (int) $duration * 60;
        }

        $hm = explode($delim, $duration);
        $hours = (int) $hm[0] * 60;
        $mins = (int) $hm[1];

        return $hours + $mins;
    }

    /**
     * @param $table
     * @return mixed
     */
    protected function getTotalRows($table)
    {
        $table = $this->tableName($table);

        $sql = 'SELECT COUNT(*) FROM '.$table;
        $rows = $this->conn->query($sql)->fetchColumn();

        $this->info('Found '.$rows.' rows in '.$table);

        return (int) $rows;
    }

    /**
     * Read all the rows in a table, but read them in a batched manner
     * @param string $table     The name of the table
     * @param null   $read_rows Number of rows to read
     * @return \Generator
     */
    protected function readRows($table, $read_rows = null)
    {
        // Set the table prefix if it has been entered
        $this->tableName($table);

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

            $this->info('Reading '.$offset.' to '.$rows_to_read.' of '.$total_rows);

            $sql = 'SELECT * FROM '.$this->tableName($table)
                .' LIMIT '.self::BATCH_READ_ROWS.' OFFSET '.$offset;

            try {
                foreach ($this->conn->query($sql) as $row) {
                    yield $row;
                }
            } catch (PDOException $e) {
                // Without incrementing the offset, it should re-run the same query
                $this->error($e);

                if (strpos($e->getMessage(), 'server has gone away') !== false) {
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
        $airline = Airline::first();
        $subfleet = Subfleet::firstOrCreate(
            ['airline_id' => $airline->id, 'name' => self::SUBFLEET_NAME],
            ['type' => 'PHPVMS']
        );

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
        foreach ($this->readRows('ranks') as $row) {
            $rank = Rank::firstOrCreate(
                ['name' => $row->rank],
                ['image_url' => $row->rankimage, 'hours' => $row->minhours]
            );

            $this->addMapping('ranks', $row->rankid, $rank->id);
            $this->addMapping('ranks', $row->rank, $rank->id);

            if ($rank->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' ranks');
    }

    /**
     * Import all of the airlines. Save them all in the private var $airlines
     * They're used to lookup from other reference tables
     */
    protected function importAirlines()
    {
        $this->comment('--- AIRLINE IMPORT ---');

        $count = 0;
        foreach ($this->readRows('airlines') as $row) {
            $airline = Airline::firstOrCreate(
                ['icao' => $row->code],
                ['iata' => $row->code, 'name' => $row->name, 'active' => $row->enabled]
            );

            $this->addMapping('airlines', $row->id, $airline->id);
            $this->addMapping('airlines', $row->code, $airline->id);

            if ($airline->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' airlines');
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
        foreach ($this->readRows('aircraft') as $row) {
            $aircraft = Aircraft::firstOrCreate(
                ['name' => $row->fullname, 'registration' => $row->registration],
                ['icao'        => $row->icao,
                 'subfleet_id' => $subfleet->id,
                 'active'      => $row->enabled
                ]);

            $this->addMapping('aircraft', $row->id, $aircraft->id);

            if ($aircraft->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' aircraft');
    }

    /**
     * Import all of the airports
     */
    protected function importAirports()
    {
        $this->comment('--- AIRPORT IMPORT ---');

        $count = 0;
        foreach ($this->readRows('airports') as $row) {
            $attrs = [
                'id'      => trim($row->icao),
                'icao'    => trim($row->icao),
                'name'    => $row->name,
                'country' => $row->country,
                'lat'     => $row->lat,
                'lon'     => $row->lng,
                'hub'     => $row->hub,
            ];

            $airport = Airport::updateOrCreate(
                ['id' => $attrs['id']],
                $attrs
            );

            if ($airport->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' airports');
    }

    /**
     * Import the flights and schedules
     */
    protected function importFlights()
    {
        $this->comment('--- FLIGHT SCHEDULE IMPORT ---');

        $count = 0;
        foreach ($this->readRows('schedules') as $row) {
            $airline_id = $this->getMapping('airlines', $row->code);

            $flight_num = trim($row->flightnum);

            $attrs = [
                'dpt_airport_id' => $row->depicao,
                'arr_airport_id' => $row->arricao,
                'route'          => $row->route ?: '',
                'distance'       => round($row->distance ?: 0, 2),
                'level'          => $row->flightlevel ?: 0,
                'dpt_time'       => $row->deptime ?: '',
                'arr_time'       => $row->arrtime ?: '',
                'flight_time'    => $this->convertDuration($row->flighttime) ?: '',
                'notes'          => $row->notes ?: '',
                'active'         => $row->enabled ?: true,
            ];

            try {
                $flight = Flight::updateOrCreate(
                    ['airline_id' => $airline_id, 'flight_number' => $flight_num],
                    $attrs
                );
            } catch (\Exception $e) {
                #$this->error($e);
            }

            $this->addMapping('flights', $row->id, $flight->id);

            // TODO: deserialize route_details into ACARS table

            if ($flight->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' flights');
    }

    /**
     * Import all of the PIREPs
     */
    protected function importPireps()
    {
        $this->comment('--- PIREP IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pireps') as $row) {
            $pirep_id = $row->pirepid;
            $user_id = $this->getMapping('users', $row->pilotid);
            $airline_id = $this->getMapping('airlines', $row->code);
            $aircraft_id = $this->getMapping('aircraft', $row->aircraft);

            $attrs = [
                #'id' => $pirep_id,
                'user_id'        => $user_id,
                'airline_id'     => $airline_id,
                'aircraft_id'    => $aircraft_id,
                'flight_number'  => $row->flightnum ?: '',
                'dpt_airport_id' => $row->depicao,
                'arr_airport_id' => $row->arricao,
                'block_fuel'     => $row->fuelused,
                'route'          => $row->route ?: '',
                'source_name'    => $row->source,
                'created_at'     => $this->parseDate($row->submitdate),
                'updated_at'     => $this->parseDate($row->modifieddate),
            ];

            # Set the distance
            $distance = round($row->distance ?: 0, 2);
            $attrs['distance'] = $distance;
            $attrs['planned_distance'] = $distance;

            # Set the flight time properly
            $duration = $this->convertDuration($row->flighttime_stamp);
            $attrs['flight_time'] = $duration;
            $attrs['planned_flight_time'] = $duration;

            # Set how it was filed
            if (strtoupper($row->source) === 'MANUAL') {
                $attrs['source'] = PirepSource::MANUAL;
            } else {
                $attrs['source'] = PirepSource::ACARS;
            }

            # Set the flight type
            $row->flighttype = strtoupper($row->flighttype);
            if ($row->flighttype === 'P') {
                $attrs['flight_type'] = FlightType::SCHED_PAX;
            } elseif ($row->flighttype === 'C') {
                $attrs['flight_type'] = FlightType::SCHED_CARGO;
            } else {
                $attrs['flight_type'] = FlightType::CHARTER_PAX_ONLY;
            }

            # Set the flight level of the PIREP is set
            if (property_exists($row, 'flightlevel')) {
                $attrs['level'] = $row->flightlevel;
            } else {
                $attrs['level'] = 0;
            }

            $pirep = Pirep::updateOrCreate(
                ['id' => $pirep_id],
                $attrs
            );

            $source = strtoupper($row->source);
            if ($source === 'SMARTCARS') {
                # TODO: Parse smartcars log into the acars table
            } elseif ($source === 'KACARS') {
                # TODO: Parse kACARS log into acars table
            } elseif ($source === 'XACARS') {
                # TODO: Parse XACARS log into acars table
            }

            # TODO: Add extra fields in as PIREP fields
            $this->addMapping('pireps', $row->pirepid, $pirep->id);

            if ($pirep->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' pireps');
    }

    protected function importUsers()
    {
        $this->comment('--- USER IMPORT ---');

        $count = 0;
        foreach ($this->readRows('pilots', 50) as $row) {
            # TODO: What to do about pilot ids

            $name = $row->firstname.' '.$row->lastname;

            $airline_id = $this->getMapping('airlines', $row->code);
            $rank_id = $this->getMapping('ranks', $row->rank);
            $state = $this->getUserState($row->retired);

            $new_password = Str::random(60);

            $attrs = [
                'name'            => $name,
                'password'        => Hash::make($new_password),
                'api_key'         => Utils::generateApiKey(),
                'airline_id'      => $airline_id,
                'rank_id'         => $rank_id,
                'home_airport_id' => $row->hub,
                'curr_airport_id' => $row->hub,
                'flights'         => (int) $row->totalflights,
                'flight_time'     => Utils::hoursToMinutes($row->totalhours),
                'state'           => $state,
                'created_at'      => $this->parseDate($row->joindate),
            ];

            $user = User::updateOrCreate(
                ['email' => $row->email],
                $attrs
            );

            $this->addMapping('users', $row->pilotid, $user->id);

            if ($user->wasRecentlyCreated) {
                ++$count;
            }
        }

        $this->info('Imported '.$count.' users');
    }

    /**
     * Go through and set the last PIREP ID for the users
     */
    protected function findLastPireps()
    {
    }

    /**
     * Recalculate all of the user ranks
     */
    protected function recalculateRanks()
    {
        /*$this->comment('--- RECALCULATING RANKS ---');*/
    }

    /**
     * Get the user's new state from their original state
     * @param $state
     * @return int
     */
    protected function getUserState($state)
    {
        // TODO: This state might differ between simpilot and classic version

        $state = (int) $state;

        // Declare array of classic states
        $phpvms_classic_states = [
            'ACTIVE'   => 0,
            'INACTIVE' => 1,
            'BANNED'   => 2,
            'ON_LEAVE' => 3
        ];

        // Decide which state they will be in accordance with v7
        if ($state === $phpvms_classic_states['ACTIVE']) {
            return UserState::ACTIVE;
        } elseif ($state === $phpvms_classic_states['INACTIVE']) {
            # TODO: Make an inactive state?
            return UserState::REJECTED;
        } elseif ($state === $phpvms_classic_states['BANNED']) {
            return UserState::SUSPENDED;
        } elseif ($state === $phpvms_classic_states['ON_LEAVE']) {
            return UserState::ON_LEAVE;
        } else {
            $this->error('Unknown status: '.$state);
        }
    }
}
