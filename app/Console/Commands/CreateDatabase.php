<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;

class CreateDatabase extends BaseCommand
{
    protected $signature = 'database:create {--reset} {--conn=?}';
    protected $description = 'Create a database';
    protected $os;

    public function __construct()
    {
        parent::__construct();
        $this->os = new \Tivie\OS\Detector();
    }

    /**
     * create the mysql database
     * @param $dbkey
     */
    protected function create_mysql($dbkey)
    {
        $exec = 'mysql';
        if ($this->os->isWindowsLike()) {
            $exec .= '.exe';
        }

        $mysql_cmd = [
            $exec,
            '-u' . config($dbkey . 'username'),
            '-h' . config($dbkey . 'host'),
            '-P' . config($dbkey . 'port'),
        ];

        # only supply password if it's set
        $password = config($dbkey . 'password');
        if ($password !== '') {
            $mysql_cmd[] = '-p' . $password;
        }

        if ($this->option('reset') === true) {
            $cmd = array_merge(
                $mysql_cmd,
                ["-e 'DROP DATABASE IF EXISTS " . config($dbkey . 'database') . "'"]
            );

            $this->runCommand($cmd);
        }

        $cmd = array_merge(
            $mysql_cmd,
            ["-e 'CREATE DATABASE IF NOT EXISTS " . config($dbkey . 'database') . "'"]
        );

        $this->runCommand($cmd);
    }

    /**
     * Create the sqlite database
     * @param $dbkey
     */
    protected function create_sqlite($dbkey)
    {
        $exec = 'sqlite3';
        if ($this->os->isWindowsLike()) {
            $exec = 'sqlite3.exe';
        }

        if ($this->option('reset') === true) {
            $cmd = ['rm', '-rf', config($dbkey . 'database')];
            $this->runCommand($cmd);
        }

        if (!file_exists(config($dbkey . 'database'))) {
            $cmd = [
                $exec,
                config($dbkey . 'database'),
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

        $this->info('Using connection "' . config('database.default') . '"');

        $conn = config('database.default');
        $dbkey = 'database.connections.' . $conn . '.';

        if (config($dbkey . 'driver') === 'mysql') {
            $this->create_mysql($dbkey);
        }

        elseif (config($dbkey . 'driver') === 'sqlite') {
            $this->create_sqlite($dbkey);
        }

        // TODO: Eventually
        elseif (config($dbkey . 'driver') === 'postgres') {
            $this->create_postgres($dbkey);
        }
    }
}
