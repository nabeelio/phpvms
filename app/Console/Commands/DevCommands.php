<?php

namespace App\Console\Commands;

use App\Services\DatabaseService;
use DB;

use App\Console\BaseCommand;
use App\Models\Acars;
use App\Models\Pirep;

class DevCommands extends BaseCommand
{
    protected $signature = 'phpvms {cmd} {--file=?}';
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
        $command = trim($this->argument('cmd'));

        if (!$command) {
            $this->error('No command specified!');
            exit();
        }

        $commands = [
            'clear-acars' => 'clearAcars',
            'compile-assets' => 'compileAssets',
            'import' => 'importYaml',
        ];

        if(!array_key_exists($command, $commands)) {
            $this->error('Command not found!');
            exit();
        }

        $this->{$commands[$command]}();
    }

    /**
     * Delete all the data from the ACARS and PIREP tables
     */
    protected function clearAcars()
    {
        if(config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=0');
        }

        Acars::truncate();
        Pirep::truncate();

        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=1');
        }

        $this->info('ACARS and PIREPs cleared!');
    }

    /**
     * Compile all the CSS/JS assets into their respective files
     * Calling the webpack compiler
     */
    protected function compileAssets()
    {
        $this->runCommand('npm update');
        $this->runCommand('npm run dev');
    }

    /**
     * Import data from a YAML file
     */
    protected function importYaml()
    {
        $this->info('importing '. $this->argument('file'));
    }
}
