<?php

namespace App\Services;

use App\Interfaces\ImportExport;
use App\Interfaces\Service;
use App\Repositories\FlightRepository;
use App\Services\ImportExport\FlightExporter;
use Illuminate\Support\Collection;
use League\Csv\CharsetConverter;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
use Log;

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
     * @param string    $path
     * @return Writer
     */
    public function openCsv($path): Writer
    {
        $writer = Writer::createFromPath($path, 'w+');
        CharsetConverter::addTo($writer, 'utf-8', 'iso-8859-15');
        return $writer;
    }

    /**
     * Run the actual importer
     * @param Collection   $collection
     * @param ImportExport $exporter
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     */
    protected function runExport(Collection $collection, ImportExport $exporter): string
    {
        $filename = 'export_' . $exporter->assetType . '.csv';

        // Create the directory - makes it inside of storage/app
        Storage::makeDirectory('import');
        $path = storage_path('/app/import/export_'.$filename.'.csv');

        $writer = $this->openCsv($path);

        // Write out the header first
        $writer->insertOne($exporter->getColumns());

        // Write the rest of the rows
        foreach ($collection as $row) {
            $writer->insertOne($exporter->export($row));
        }

        return $path;
    }

    /**
     * Export all of the flights
     * @param Collection  $flights
     * @param string      $csv_file
     * @return mixed
     * @throws \League\Csv\Exception
     */
    public function exportFlights($flights)
    {
        $exporter = new FlightExporter();
        return $this->runExport($flights, $exporter);
    }
}
