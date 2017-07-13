<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class Install extends Command
{
    protected $signature = 'phpvms:install {--update}';
    protected $description = 'Install or update phpVMS';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Installing phpVMS...');

        $this->setupDatabase();
        $this->initialData();
    }

    /**
     * 1. Setup the database and run the migrations
     *    Only call the database creation if we're not
     *    explicitly trying to upgrade
     */
    protected function setupDatabase()
    {
        if(!$this->option('update')) {
            $this->call('database:create');
        }

        $this->call('migrate:refresh');

        # TODO: Call initial seed data, for the groups and other supporting data
    }

    /**
     * 2. Set an initial airline and admin user/password
     */
    protected function initialData()
    {
        # TODO: Prompt for initial airline name
        # TODO: Prompt for admin user/password
    }
}
