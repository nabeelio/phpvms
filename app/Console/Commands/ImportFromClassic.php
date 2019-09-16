<?php

namespace App\Console\Commands;

use App\Contracts\Command;

class ImportFromClassic extends Command
{
    protected $signature = 'phpvms:importer {db_host} {db_name} {db_user} {db_pass?} {table_prefix=phpvms_}';
    protected $description = 'Import from an older version of phpVMS';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $db_creds = [
            'host'         => $this->argument('db_host'),
            'name'         => $this->argument('db_name'),
            'user'         => $this->argument('db_user'),
            'pass'         => $this->argument('db_pass'),
            'table_prefix' => $this->argument('table_prefix'),
        ];

        $importerSvc = new \App\Console\Services\Importer($db_creds);
        $importerSvc->run();
    }
}
