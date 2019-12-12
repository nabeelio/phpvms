<?php

namespace Modules\Importer\Console\Commands;

use App\Contracts\Command;
use Illuminate\Support\Facades\Log;
use Modules\Importer\Services\ImporterService;

class ImportFromClassicCommand extends Command
{
    protected $signature = 'phpvms:importer {db_host} {db_name} {db_user} {db_pass?} {table_prefix=phpvms_}';
    protected $description = 'Import from an older version of phpVMS';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $creds = [
            'host'         => $this->argument('db_host'),
            'name'         => $this->argument('db_name'),
            'user'         => $this->argument('db_user'),
            'pass'         => $this->argument('db_pass'),
            'table_prefix' => $this->argument('table_prefix'),
        ];

        $importerSvc = new ImporterService();

        $importerSvc->saveCredentials($creds);
        $manifest = $importerSvc->generateImportManifest();

        foreach ($manifest as $record) {
            try {
                $importerSvc->run($record['importer'], $record['start']);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
