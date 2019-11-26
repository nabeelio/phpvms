<?php

namespace Modules\Installer\Console\Commands;

use App\Contracts\Command;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Services\Importer\ImporterService;

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

        $stage = 'stage1';
        $start = 0;

        while (true) {
            try {
                $importerSvc->run($stage, $start);
            } catch (ImporterNextRecordSet $e) {
                Log::info('More records, starting from '.$e->nextOffset);
                $start = $e->nextOffset;
            } catch (StageCompleted $e) {
                $stage = $e->nextStage;
                $start = 0;

                Log::info('Stage '.$stage.' completed, moving to '.$e->nextStage);
                if ($e->nextStage === 'complete') {
                    break;
                }
            }
        }
    }
}
