<?php

namespace App\Console\Commands;

use App\Contracts\Command;
use Illuminate\Support\Facades\Log;
use Tivie\OS\Detector;

class CreateDatabase extends Command
{
    protected $signature = 'database:create {--reset} {--conn=?}';
    protected $description = 'Create a database';
    protected $os;

    /**
     * CreateDatabase constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->os = new Detector();
    }

    /**
     * Create the mysql database
     *
     * @param $dbkey
     *
     * @return bool
     */
    protected function create_mysql($dbkey)
    {
        $host = config($dbkey.'host');
        $port = config($dbkey.'port');
        $name = config($dbkey.'database');
        $user = config($dbkey.'username');
        $pass = config($dbkey.'password');

        $dbSvc = new \App\Console\Services\Database();

        $dsn = $dbSvc->createDsn($host, $port);
        Log::info('Connection string: '.$dsn);

        try {
            $conn = $dbSvc->createPDO($dsn, $user, $pass);
        } catch (\PDOException $e) {
            Log::error($e);

            return false;
        }

        if ($this->option('reset') === true) {
            $sql = "DROP DATABASE IF EXISTS `$name`";

            try {
                Log::info('Dropping database: '.$sql);
                $conn->exec($sql);
            } catch (\PDOException $e) {
                Log::error($e);
            }
        }

        $sql = "CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET UTF8 COLLATE utf8_unicode_ci";

        try {
            Log::info('Creating database: '.$sql);
            $conn->exec($sql);
        } catch (\PDOException $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Create the sqlite database
     *
     * @param $dbkey
     */
    protected function create_sqlite($dbkey)
    {
        $exec = 'sqlite3';
        if ($this->os->isWindowsLike()) {
            $exec = 'sqlite3.exe';
        }

        if ($this->option('reset') === true) {
            $cmd = ['rm', '-rf', config($dbkey.'database')];
            $this->runCommand($cmd);
        }

        if (!file_exists(config($dbkey.'database'))) {
            $cmd = [
                $exec,
                config($dbkey.'database'),
                '""',
            ];

            $this->runCommand($cmd);
        }
    }

    protected function create_postgres($dbkey)
    {
        $this->error('Not supported yet!');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*if ($this->option('reset')) {
            if(!$this->confirm('The "reset" option will destroy the database, are you sure?')) {
                return false;
            }
        }*/

        $this->info('Using connection "'.config('database.default').'"');

        $conn = config('database.default');
        $dbkey = 'database.connections.'.$conn.'.';

        if (config($dbkey.'driver') === 'mysql') {
            $this->create_mysql($dbkey);
        } elseif (config($dbkey.'driver') === 'sqlite') {
            $this->create_sqlite($dbkey);
        } // TODO: Eventually
        elseif (config($dbkey.'driver') === 'postgres') {
            $this->create_postgres($dbkey);
        }
    }
}
