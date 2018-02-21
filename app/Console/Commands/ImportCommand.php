<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Services\DatabaseService;

class ImportCommand extends BaseCommand
{
    protected $signature = 'phpvms:import {files*}';
    protected $description = 'Developer commands';

    protected $dbSvc;

    public function __construct(DatabaseService $dbSvc)
    {
        parent::__construct();
        $this->dbSvc = $dbSvc;
    }

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $files = $this->argument('files');
        if(empty($files)) {
            $this->error('No files to import specified!');
            exit();
        }

        $ignore_errors = true;
        /*$ignore_errors = $this->option('ignore_errors');
        if(!$ignore_errors) {
            $ignore_errors = false;
        }*/

        foreach($files as $file) {
            if(!file_exists($file)) {
                $this->error('File ' . $file .' doesn\'t exist');
                exit;
            }

            $this->info('Importing ' . $file);

            $imported = $this->dbSvc->seed_from_yaml_file($file, $ignore_errors);
            foreach($imported as $table => $count) {
                $this->info('Imported '.$count.' records from "'.$table.'"');
            }
        }
    }
}
