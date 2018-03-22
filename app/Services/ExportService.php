<?php

namespace App\Services;

use App\Interfaces\ImportExport;
use App\Interfaces\Service;
use App\Repositories\FlightRepository;
use App\Services\Import\FlightExporter;
use Illuminate\Support\Collection;
use League\Csv\CharsetConverter;
use League\Csv\Writer;

/**
 * Class ExportService
 * @package App\Services
 */
class ExportService extends Service
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
     * @return Writer
     */
    public function openCsv($csv_file): Writer
    {
        $writer = Writer::createFromPath($csv_file, 'w+');
        CharsetConverter::addTo($writer, 'utf-8', 'iso-8859-15');
        return $writer;
    }

    /**
     * Run the actual importer
     * @param Collection   $collection
     * @param Writer       $writer
     * @param ImportExport $exporter
     * @return bool
     * @throws \League\Csv\CannotInsertRecord
     */
    protected function runExport(Collection $collection, Writer $writer, ImportExport $exporter): bool
    {
        $writer->insertOne($exporter->getColumns());
        foreach ($collection as $row) {
            $writer->insertOne($exporter->export($row));
        }

        return true;
    }

    /**
     * Export all of the flights
     * @param Collection  $flights
     * @param string      $csv_file
     * @return mixed
     * @throws \League\Csv\Exception
     */
    public function exportFlights($flights, $csv_file)
    {
        $writer = $this->openCsv($csv_file);

        $exporter = new FlightExporter();
        return $this->runExport($flights, $writer, $exporter);
    }
}
