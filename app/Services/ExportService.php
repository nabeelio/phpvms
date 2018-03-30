<?php

namespace App\Services;

use App\Interfaces\ImportExport;
use App\Interfaces\Service;
use App\Services\ImportExport\AircraftExporter;
use App\Services\ImportExport\AirportExporter;
use App\Services\ImportExport\ExpenseExporter;
use App\Services\ImportExport\FareExporter;
use App\Services\ImportExport\FlightExporter;
use App\Services\ImportExport\SubfleetExporter;
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

        Log::info('Exporting "'.$exporter->assetType.'" to ' . $path);

        $writer = $this->openCsv($path);

        // Write out the header first
        $writer->insertOne(array_keys($exporter->getColumns()));

        // Write the rest of the rows
        foreach ($collection as $row) {
            $writer->insertOne($exporter->export($row));
        }

        return $path;
    }

    /**
     * Export all of the aircraft
     * @param Collection $aircraft
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportAircraft($aircraft)
    {
        $exporter = new AircraftExporter();
        return $this->runExport($aircraft, $exporter);
    }

    /**
     * Export all of the airports
     * @param Collection $airports
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportAirports($airports)
    {
        $exporter = new AirportExporter();
        return $this->runExport($airports, $exporter);
    }

    /**
     * Export all of the airports
     * @param Collection $expenses
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportExpenses($expenses)
    {
        $exporter = new ExpenseExporter();
        return $this->runExport($expenses, $exporter);
    }

    /**
     * Export all of the fares
     * @param Collection $fares
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportFares($fares)
    {
        $exporter = new FareExporter();
        return $this->runExport($fares, $exporter);
    }

    /**
     * Export all of the flights
     * @param Collection $flights
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportFlights($flights)
    {
        $exporter = new FlightExporter();
        return $this->runExport($flights, $exporter);
    }

    /**
     * Export all of the flights
     * @param Collection $subfleets
     * @return mixed
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportSubfleets($subfleets)
    {
        $exporter = new SubfleetExporter();
        return $this->runExport($subfleets, $exporter);
    }
}
