<?php

namespace App\Services\Installer;

use App\Interfaces\Service;
use App\Models\Setting;
use DB;
use Log;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MigrationsService
 * @package Modules\Installer\Services
 */
class MigrationService extends Service
{
    private $counters = [];
    private $offsets = [];

    protected function getMigrator()
    {
        $m = app('migrator');
        $m->setConnection(config('database.default'));
        return $m;
    }

    /**
     * Syncronize all of the seed files, run this after the
     */
    public function syncAllSeeds(): void
    {
        $this->syncAllSettings();
    }

    public function syncAllSettings(): void {
        $data = file_get_contents(database_path('/seeds/settings.yml'));
        $yml = Yaml::parse($data);
        foreach ($yml as $setting) {
            if (\trim($setting['key']) === '') {
                continue;
            }

            $this->addSetting($setting['key'], $setting);
        }
    }

    /**
     * @param $key
     * @param $attrs
     */
    public function addSetting($key, $attrs): void
    {
        $id = Setting::formatKey($key);
        $group = $attrs['group'];
        $order = $this->getNextOrderNumber($group);

        $attrs = array_merge(
            [
                'id'          => $id,
                'key'         => $key,
                'offset'      => $this->offsets[$group],
                'order'       => $order,
                'name'        => '',
                'group'       => $group,
                'value'       => '',
                'default'     => $attrs['value'],
                'options'     => '',
                'type'        => 'hidden',
                'description' => '',
            ],
            $attrs
        );

        $count = DB::table('settings')->where('id', $id)->count('id');
        if ($count === 0) {
            DB::table('settings')->insert($attrs);
        } else {
            unset($attrs['value']);  // Don't overwrite this
            DB::table('settings')
                ->where('id', $id)
                ->update($attrs);
        }
    }

    /**
     * Dynamically figure out the offset and the start number for a group.
     * This way we don't need to mess with how to order things
     * When calling getNextOrderNumber(users) 31, will be returned, then 32, and so on
     *
     * @param      $name
     * @param null $offset
     * @param int  $start_offset
     */
    private function addCounterGroup($name, $offset = null, $start_offset = 0): void
    {
        if ($offset === null) {
            $group = DB::table('settings')
                ->where('group', $name)
                ->first();

            if ($group === null) {
                $offset = (int)DB::table('settings')->max('offset');
                if ($offset === null) {
                    $offset = 0;
                    $start_offset = 1;
                } else {
                    $offset += 100;
                    $start_offset = $offset + 1;
                }
            } else {
                // Now find the number to start from
                $start_offset = (int)DB::table('settings')->where('group', $name)->max('order');
                if ($start_offset === null) {
                    $start_offset = $offset + 1;
                } else {
                    $start_offset++;
                }

                $offset = $group->offset;
            }
        }

        $this->counters[$name] = $start_offset;
        $this->offsets[$name] = $offset;
    }

    /**
     * Get the next increment number from a group
     *
     * @param $group
     *
     * @return int
     */
    private function getNextOrderNumber($group): int
    {
        if (!\in_array($group, $this->counters, true)) {
            $this->addCounterGroup($group);
        }

        $idx = $this->counters[$group];
        $this->counters[$group]++;

        return $idx;
    }

    /**
     * Find all of the possible paths that migrations exist.
     * Include looking in all of the modules Database/migrations directories
     * @return array
     */
    public function getMigrationPaths(): array
    {
        $paths = [
            'core' => \App::databasePath() . '/migrations'
        ];

        $modules = Module::allEnabled();
        foreach ($modules as $module) {
            $module_path = $module->getPath() . '/Database/migrations';
            if(file_exists($module_path)) {
                $paths[$module->getName()] = $module_path;
            }
        }

        Log::info('Update - migration paths', $paths);

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
        $availMigrations = array_diff(array_keys($files), $migrator->getRepository()->getRan());

        Log::info('Migrations available:', $availMigrations);

        return $availMigrations;
    }

    /**
     * Run all of the migrations that are available. Just call artisan since
     * it looks into all of the module directories, etc
     */
    public function runAllMigrations()
    {
        $output = '';

        \Artisan::call('migrate');
        $output .= trim(\Artisan::output());

        return $output;
    }
}
