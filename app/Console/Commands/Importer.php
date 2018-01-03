<?php

namespace App\Console\Commands;

use DB;
use App\Console\BaseCommand;

class Importer extends BaseCommand
{
    protected $signature = 'phpvms:importer {db_host} {db_name} {db_user} {db_pass?}';
    protected $description = 'Import from an older version of phpVMS';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $db_creds = [
            'host' => $this->argument('db_host'),
            'name' => $this->argument('db_name'),
            'user' => $this->argument('db_user'),
            'pass' => $this->argument('db_pass')
        ];

        $importerSvc = new \App\Console\Services\Importer($db_creds);
        $importerSvc->run();
    }
}
