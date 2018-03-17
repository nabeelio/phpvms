<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use Modules\Installer\Services\ConfigService;

/**
 * Create a fresh development install
 * @package App\Console\Commands
 */
class DevInstall extends BaseCommand
{
    protected $signature = 'phpvms:dev-install {--reset-db}';
    protected $description = 'Run a developer install and run the sample migration';

    /**
     * Run dev related commands
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function handle()
    {
        if(!$this->option('reset-db')) {
            $this->rewriteConfigs();
        }

        # Reload the configuration
        \App::boot();

        $this->info('Recreating database');
        \Artisan::call('database:create', [
            '--reset' => true,
        ]);

        $this->info(\Artisan::output());

        #
        #

        $this->info('Running migrations');
        \Artisan::call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->info(\Artisan::output());

        #
        #

        $this->info('Importing sample data');
        \Artisan::call('phpvms:import', [
            'files' => ['app/Database/seeds/sample.yml'],
        ]);

        $this->info(\Artisan::output());

        $this->info('Done!');
    }

    /**
     * Rewrite the configuration files
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function rewriteConfigs()
    {
        $cfgSvc = app(ConfigService::class);

        $this->info('Removing the old config files');

        # Remove the old files
        $config_file = base_path('config.php');
        if(file_exists($config_file)) {
            unlink($config_file);
        }

        $env_file = base_path('env.php');
        if(file_exists($env_file)) {
            unlink($env_file);
        }

        $this->info('Removing the sqlite db');
        $db_file = storage_path('db.sqlite');
        if(file_exists($db_file)) {
            unlink($db_file);
        }

        $this->info('Regenerating the config files');
        $cfgSvc->createConfigFiles([
            'APP_ENV' => 'dev',
            'SITE_NAME' => 'phpvms test',
            'DB_CONN' => 'sqlite',
        ]);

        $this->info('Config files generated!');
    }
}
