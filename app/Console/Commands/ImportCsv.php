<?php
/**
 *
 */

namespace App\Console\Commands;

use App\Console\Command;
use App\Services\ImportService;

/**
 * Class ImportCsv
 * @package App\Console\Commands
 */
class ImportCsv extends Command
{
    protected $signature = 'phpvms:csv-import {type} {file}';
    protected $description = 'Import from a CSV file';

    private $importer;

    /**
     * Import constructor.
     * @param ImportService $importer
     */
    public function __construct(ImportService $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    /**
     * @return mixed|void
     * @throws \League\Csv\Exception
     */
    public function handle()
    {
        $type = $this->argument('type');
        $file = $this->argument('file');

        if (\in_array($type, ['flight', 'flights'])) {
            $status = $this->importer->importFlights($file);
        } elseif ($type === 'aircraft') {
            $status = $this->importer->importAircraft($file);
        } elseif (\in_array($type, ['airport', 'airports'])) {
            $status = $this->importer->importAirports($file);
        } elseif ($type === 'subfleet') {
            $status = $this->importer->importSubfleets($file);
        }

        foreach($status['success'] as $line) {
            $this->info($line);
        }

        foreach ($status['failed'] as $line) {
            $this->error($line);
        }
    }
}
