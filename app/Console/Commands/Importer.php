<?php

namespace App\Console\Commands;

use DB;
use App\Console\BaseCommand;

class Importer extends BaseCommand
{
    protected $signature = 'phpvms:importer';
    protected $description = 'Import from an older version of phpVMS';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $db_creds = [
            'host' => $this->ask('db_host'),
            'name' => $this->ask('db_name'),
            'user' => $this->ask('db_user'),
            'pass' => $this->ask('db_pass'),
            'table_prefix' => $this->ask('table_prefix', false)
        ];

        $importerSvc = new \App\Console\Services\Importer($db_creds);
        $importerSvc->run();
    }
}
