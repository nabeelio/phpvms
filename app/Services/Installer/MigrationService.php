<?php

namespace App\Services\Installer;

use App\Contracts\Service;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class MigrationService extends Service
{
    protected function getMigrator()
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

        // Log::info('Update - migration paths', $paths);

        return $paths;
    }

    /**
     * Return what migrations are available
     */
    public function migrationsAvailable(): array
    {
        $migrator = $this->getMigrator();
        $migration_dirs = $this->getMigrationPaths();

        $files = $migrator->getMigrationFiles(array_values($migration_dirs));
        // Log::info('Migrations available:', $availMigrations);

        return array_diff(array_keys($files), $migrator->getRepository()->getRan());
    }

    /**
     * Run all of the migrations that are available. Just call artisan since
     * it looks into all of the module directories, etc
     */
    public function runAllMigrations()
    {
        $output = '';

        Artisan::call('migrate');
        $output .= trim(Artisan::output());

        $modules = Module::allEnabled();
        foreach ($modules as $module) {
            $module_path = $module->getPath().'/Database/migrations';
            if (file_exists($module_path)) {
                Artisan::call('module:migrate '.$module->getName());
                $output .= trim(Artisan::output());
            }
        }

        return $output;
    }
}
