<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class CreateDatabase extends Command
{
    protected $signature = 'database:create {--reset} {--conn=?}';
    protected $description = 'Create a database';

    public function __construct()
    {
        parent::__construct();
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

    protected function create_mysql($dbkey)
    {
        $mysql_cmd = [
            'mysql',
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

    protected function create_sqlite($dbkey)
    {
        if ($this->option('reset')) {
            $cmd = ['rm', '-rf', config($dbkey . 'database')];
            $this->runCommand($cmd);
        }

        $cmd = [
            'sqlite3',
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
