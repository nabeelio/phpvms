<?php

namespace App\Services;

use App\Interfaces\ImportExport;
use App\Interfaces\Service;
use App\Models\Airport;
use App\Repositories\FlightRepository;
use App\Services\Import\AircraftImporter;
use App\Services\Import\AirportImporter;
use App\Services\Import\FlightImporter;
use App\Services\Import\SubfleetImporter;
use League\Csv\Reader;

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
     * @param      $csv_file
     * @return Reader
     * @throws \League\Csv\Exception
     */
    public function openCsv($csv_file)
    {
        $reader = Reader::createFromPath($csv_file);
        $reader->setDelimiter(',');
        $reader->setEnclosure('"');

        return $reader;
    }

    /**
     * Run the actual importer
     * @param Reader       $reader
     * @param ImportExport $importer
     * @return array
     */
    protected function runImport(Reader $reader, ImportExport $importer): array
    {
        $import_report = [
            'success' => [],
            'failed'  => [],
        ];

        $cols = $importer->getColumns();
        $first_header = $cols[0];

        $records = $reader->getRecords($cols);
        foreach ($records as $offset => $row) {
            // check if the first row being read is the header
            if ($row[$first_header] === $first_header) {
                continue;
            }

            $success = $importer->import($row, $offset);
            if ($success) {
                $import_report['success'][] = $importer->status;
            } else {
                $import_report['failed'][] = $importer->status;
            }
        }

        return $import_report;
    }

    /**
     * Import aircraft
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws \League\Csv\Exception
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
     * @throws \League\Csv\Exception
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
     * Import flights
     * @param string $csv_file
     * @param bool   $delete_previous
     * @return mixed
     * @throws \League\Csv\Exception
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
     * @throws \League\Csv\Exception
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
