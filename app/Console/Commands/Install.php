<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;

class Install extends BaseCommand
{
    protected $signature = 'phpvms:install 
                                {--update}
                                {--airline-name?} 
                                {--airline-code?}';

    protected $description = 'Install or update phpVMS';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Installing phpVMS...');

        $this->setupDatabase();

        # Only run these if we're doing an initial install
        if(!$this->option('update')) {
            $this->writeLocalConfig();
            $this->initialData();
        }
    }

    /**
     * Setup the database and run the migrations
     * Only call the database creation if we're not
     * explicitly trying to upgrade
     */
    protected function setupDatabase()
    {
        if(!$this->option('update')) {
            $this->call('database:create');
        }

        $this->info('Running database migrations...');
        $this->call('migrate:refresh');

        # TODO: Call initial seed data, for the groups and other supporting data
    }

    /**
     * Write a local config file
     */
    protected function writeLocalConfig()
    {

    }

    /**
     * Set an initial airline and admin user/password
     */
    protected function initialData()
    {
        # TODO: Prompt for initial airline info
        $airline_name = $this->option('airline-name');
        if(!$airline_name) {
            $airline_name = $this->ask('Enter your airline name');
        }

        $airline_code = $this->option('airline-code');
        if(!$airline_code) {
            $airline_code = $this->ask('Enter your airline code');
        }

        # TODO: Prompt for admin user/password
    }
}
