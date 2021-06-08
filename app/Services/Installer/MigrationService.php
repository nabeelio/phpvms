<?php

namespace App\Services\Installer;

use App\Contracts\Service;
use Exception;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;

class MigrationService extends Service
{
    protected function getMigrator(): Migrator
    {
        $m = app('migrator');
        $m->setConnection(config('database.default'));
        return $m;
    }

    /**
     * Find all of the possible paths that migrations exist.
     * Include looking in all of the modules Database/migrations directories
     *
     * @return array
     */
    public function getMigrationPaths(): array
    {
        $paths = [
            'core' => App::databasePath().'/migrations',
        ];

        $modules = Module::allEnabled();
        foreach ($modules as $module) {
            $module_path = $module->getPath().'/Database/migrations';
            if (file_exists($module_path)) {
                $paths[$module->getName()] = $module_path;
            }
        }

        return $paths;
    }

    /**
     * Return what migrations are available
     */
    public function migrationsAvailable(): array
    {
        $migrator = $this->getMigrator();
        $migration_dirs = $this->getMigrationPaths();

        $availMigrations = [];
        $runFiles = [];

        try {
            $runFiles = $migrator->getRepository()->getRan();
        } catch (Exception $e) {
        } // Skip database run initialized

        $files = $migrator->getMigrationFiles(array_values($migration_dirs));

        foreach ($files as $filename => $filepath) {
            if (in_array($filename, $runFiles, true)) {
                continue;
            }

            $availMigrations[] = $filepath;
        }

        //Log::info('Migrations available: '.count($availMigrations));

        return $availMigrations;
    }

    /**
     * Run all of the migrations that are available. Just call artisan since
     * it looks into all of the module directories, etc
     */
    public function runAllMigrations(): string
    {
        // A little ugly, run the main migration first, this makes sure the migration table is there
        $output = '';

        Artisan::call('migrate', ['--force' => true]);
        $output .= trim(Artisan::output());

        // Then get any remaining migrations that are left over
        // Due to caching or whatever reason, the migrations are not all loaded when Artisan first
        // runs. This is likely a side effect of the database being used as the module activator,
        // and the list of migrations being pulled before the initial modules are populated
        $migrator = $this->getMigrator();
        $availMigrations = $this->migrationsAvailable();

        if (count($availMigrations) > 0) {
            Log::info('Running '.count($availMigrations).' available migrations');
            $ret = $migrator->run($availMigrations);
            Log::info('Ran '.count($ret).' migrations');

            return $output."\n".implode("\n", $ret);
        }

        return $output;
    }
}
