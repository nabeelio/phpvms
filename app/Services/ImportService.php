<?php

namespace App\Services;

use App\Interfaces\ImportExport;
use App\Interfaces\Service;
use App\Models\Airport;
use App\Models\Expense;
use App\Repositories\FlightRepository;
use App\Services\ImportExport\AircraftImporter;
use App\Services\ImportExport\AirportImporter;
use App\Services\ImportExport\ExpenseImporter;
use App\Services\ImportExport\FareImporter;
use App\Services\ImportExport\FlightImporter;
use App\Services\ImportExport\SubfleetImporter;
use Illuminate\Validation\ValidationException;
use League\Csv\Exception;
use League\Csv\Reader;
use Log;
use Validator;

/**
 * Class ImportService
 * @package App\Services
 */
class ImportService extends Service
{
    protected $flightRepo;

    /**
     * ImporterService constructor.
     * @param FlightRepository $flightRepo
     */
    public function __construct(FlightRepository $flightRepo) {
        $this->flightRepo = $flightRepo;
    }

    /**
     * @param $error
     * @param $e
     * @throws ValidationException
     */
    protected function throwError($error, \Exception $e= null): void
    {
        Log::error($error);
        if($e) {
            Log::error($e->getMessage());
        }

        $validator = Validator::make([], []);
        $validator->errors()->add('csv_file', $error);
        throw new ValidationException($validator);
    }

    /**
     * @param      $csv_file
     * @return Reader
     * @throws ValidationException
     */
    public function openCsv($csv_file)
    {
        try {
            $reader = Reader::createFromPath($csv_file);
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            return $reader;
        } catch (Exception $e) {
            $this->throwError('Error opening CSV: '.$e->getMessage(), $e);
        }
    }

    /**
     * Run the actual importer, pass in one of the Import classes which implements
     * the ImportExport interface
     * @param Reader       $reader
     * @param ImportExport $importer
     * @return array
     * @throws ValidationException
     */
    protected function runImport(Reader $reader, ImportExport $importer): array
    {
        $cols = $importer->getColumns();
        $first_header = $cols[0];

        $first = true;
        $records = $reader->getRecords($cols);
        foreach ($records as $offset => $row) {
            // check if the first row being read is the header
            if ($first) {
                $first = false;

                if($row[$first_header] !== $first_header) {
                    $this->throwError('CSV file doesn\'t seem to match import type');
                }

                continue;
            }

            // Do a sanity check on the number of columns first
            if (!$importer->checkColumns($row)) {
                $importer->errorLog('Number of columns in row doesn\'t match');
                continue;
            }

            $importer->import($row, $offset);
        }

        return $importer->status;
    }

    /**
     * Import aircraft
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importAircraft($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            # TODO: delete airports
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            return false;
        }

        $importer = new AircraftImporter();
        return $this->runImport($reader, $importer);
    }

    /**
     * Import airports
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importAirports($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            Airport::truncate();
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            return false;
        }

        $importer = new AirportImporter();
        return $this->runImport($reader, $importer);
    }

    /**
     * Import expenses
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importExpenses($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            Expense::truncate();
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            return false;
        }

        $importer = new ExpenseImporter();
        return $this->runImport($reader, $importer);
    }

    /**
     * Import fares
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importFares($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            # TODO: Delete all from: fares
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            # TODO: Throw an error
            return false;
        }

        $importer = new FareImporter();
        return $this->runImport($reader, $importer);
    }

    /**
     * Import flights
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importFlights($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            # TODO: Delete all from: flights, flight_field_values
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            # TODO: Throw an error
            return false;
        }

        $importer = new FlightImporter();
        return $this->runImport($reader, $importer);
    }

    /**
     * Import subfleets
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws ValidationException
     */
    public function importSubfleets($csv_file, bool $delete_previous = true)
    {
        if ($delete_previous) {
            # TODO: Cleanup subfleet data
        }

        $reader = $this->openCsv($csv_file);
        if (!$reader) {
            # TODO: Throw an error
            return false;
        }

        $importer = new SubfleetImporter();
        return $this->runImport($reader, $importer);
    }
}
