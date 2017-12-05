<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class CreateDatabase extends Command
{
    protected $signature = 'database:create {--reset} {--conn=?}';
    protected $description = 'Create a database';
    protected $os;

    public function __construct()
    {
        parent::__construct();
        $this->os = new \Tivie\OS\Detector();
    }

    protected function runCommand($cmd)
    {
        $cmd = join(' ', $cmd);

        $this->info('Running "' . $cmd . '"');

        $proc = new Process($cmd);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new ProcessFailedException($proc);
        }

        echo $proc->getOutput();
    }

    /**
     * create the mysql database
     * @param $dbkey
     */
    protected function create_mysql($dbkey)
    {
        $exec = 'mysql';
        if($this->os->isWindowsLike()) {
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
        if($password !== '') {
            $mysql_cmd[] = '-p' . $password;
        }

        if ($this->option('reset')) {
            $cmd = array_merge(
                $mysql_cmd,
                ["-e 'DROP DATABASE ".config($dbkey . 'database')."'"]
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
            $exec .= 'sqlite3';
        }

        if ($this->option('reset')) {
            $cmd = ['rm', '-rf', config($dbkey . 'database')];
            $this->runCommand($cmd);
        }

        $cmd = [
            $exec,
            config($dbkey . 'database'),
            '""',
        ];

        $this->runCommand($cmd);
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

        if(config($dbkey.'driver') === 'mysql') {
            $this->create_mysql($dbkey);
        }

        elseif (config($dbkey . 'driver') === 'sqlite') {
            $this->create_sqlite($dbkey);
        }
    }
}
