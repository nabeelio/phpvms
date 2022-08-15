<?php

namespace App\Console\Commands;

use App\Contracts\Command;
use App\Services\Installer\ConfigService;
use App\Services\Installer\SeederService;
use DatabaseSeeder;
use Illuminate\Support\Facades\App;

/**
 * Create the config files
 */
class CreateConfigs extends Command
{
    protected $signature = 'phpvms:config {db_host} {db_name} {db_user} {db_pass}';
    protected $description = 'Create the config files';

    private DatabaseSeeder $databaseSeeder;
    private SeederService $seederSvc;

    public function __construct(DatabaseSeeder $databaseSeeder, SeederService $seederSvc)
    {
        parent::__construct();

        $this->databaseSeeder = $databaseSeeder;
        $this->seederSvc = $seederSvc;
    }

    /**
     * Run dev related commands
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function handle()
    {
        $this->writeConfigs();

        // Reload the configuration
        App::boot();

        $this->info('Recreating database');
        $this->call('database:create', [
            '--reset' => true,
        ]);

        $this->info('Running migrations');
        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->seederSvc->syncAllSeeds();

        $this->info('Done!');
    }

    /**
     * Rewrite the configuration files
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    protected function writeConfigs()
    {
        /** @var ConfigService $cfgSvc */
        $cfgSvc = app(ConfigService::class);

        $this->info('Removing the old config files');

        // Remove the old files
        $config_file = base_path('config.php');
        if (file_exists($config_file)) {
            unlink($config_file);
        }

        $env_file = base_path('env.php');
        if (file_exists($env_file)) {
            unlink($env_file);
        }

        //{name} {db_host} {db_name} {db_user} {db_pass}

        $this->info('Regenerating the config files');
        $cfgSvc->createConfigFiles([
            'APP_ENV'       => 'dev',
            'SITE_NAME'     => $this->argument('name'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST'       => $this->argument('db_host'),
            'DB_DATABASE'   => $this->argument('db_name'),
            'DB_USERNAME'   => $this->argument('db_user'),
            'DB_PASSWORD'   => $this->argument('db_pass'),
        ]);

        $this->info('Config files generated!');
    }
}
