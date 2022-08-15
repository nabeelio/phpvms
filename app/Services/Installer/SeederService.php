<?php

namespace App\Services\Installer;

use App\Contracts\Service;
use App\Models\Setting;
use App\Services\DatabaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml;
use function trim;

class SeederService extends Service
{
    private DatabaseService $databaseSvc;

    private array $counters = [];
    private array $offsets = [];

    // Map an environment to a seeder directory, if we want to share
    public static $seed_mapper = [
        'production' => 'prod',
    ];

    public function __construct(DatabaseService $databaseSvc)
    {
        $this->databaseSvc = $databaseSvc;
    }

    /**
     * See if there are any seeds that are out of sync
     *
     * @return bool
     */
    public function seedsPending(): bool
    {
        if ($this->settingsSeedsPending()) {
            return true;
        }

        if ($this->permissionsSeedsPending()) {
            return true;
        }

        return false;
    }

    /**
     * Syncronize all of the seed files, run this after the migrations
     * and on first install.
     */
    public function syncAllSeeds(): void
    {
        $this->syncAllSettings();
        $this->syncAllPermissions();
        $this->syncAllModules();
        $this->syncAllYamlFileSeeds();
    }

    /**
     * Read all of the YAML files from disk and seed them
     */
    public function syncAllYamlFileSeeds(): void
    {
        Log::info('Running seeder');
        $env = App::environment();
        if (array_key_exists($env, self::$seed_mapper)) {
            $env = self::$seed_mapper[$env];
        }

        // Gather all of the files to seed
        collect()
            ->concat(Storage::disk('seeds')->files($env))
            ->map(function ($file) {
                return database_path('seeds/'.$file);
            })
            ->filter(function ($file) {
                $info = pathinfo($file);
                return $info['extension'] === 'yml';
            })
            ->each(function ($file) {
                Log::info('Seeding .'.$file);
                $this->databaseSvc->seed_from_yaml_file($file);
            });
    }

    public function syncAllModules(): void
    {
        $data = file_get_contents(database_path('/seeds/modules.yml'));
        $yml = Yaml::parse($data);
        foreach ($yml as $module) {
            $module['updated_at'] = Carbon::now('UTC');
            $count = DB::table('modules')->where('name', $module['name'])->count('name');
            if ($count === 0) {
                $module['created_at'] = Carbon::now('UTC');
                DB::table('modules')->insert($module);
            } else {
                DB::table('modules')
                    ->where('name', $module['name'])
                    ->update($module);
            }
        }
    }

    public function syncAllSettings(): void
    {
        $data = file_get_contents(database_path('/seeds/settings.yml'));
        $yml = Yaml::parse($data);
        foreach ($yml as $setting) {
            if (trim($setting['key']) === '') {
                continue;
            }

            $this->addSetting($setting['key'], $setting);
        }
    }

    public function syncAllPermissions(): void
    {
        $data = file_get_contents(database_path('/seeds/permissions.yml'));
        $yml = Yaml::parse($data);
        foreach ($yml as $perm) {
            $count = DB::table('permissions')->where('name', $perm['name'])->count('name');
            if ($count === 0) {
                DB::table('permissions')->insert($perm);
            } else {
                DB::table('permissions')
                    ->where('name', $perm['name'])
                    ->update($perm);
            }
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
                'value'       => $attrs['value'],
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
                $offset = (int) DB::table('settings')->max('offset');
                if ($offset === null) {
                    $offset = 0;
                    $start_offset = 1;
                } else {
                    $offset += 100;
                    $start_offset = $offset + 1;
                }
            } else {
                // Now find the number to start from
                $start_offset = (int) DB::table('settings')->where('group', $name)->max('order');
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
     * See if there are seeds pending for the settings
     *
     * @return bool
     */
    private function settingsSeedsPending(): bool
    {
        $all_settings = DB::table('settings')->get();
        $data = file_get_contents(database_path('/seeds/settings.yml'));
        $yml = Yaml::parse($data);

        // See if any are missing from the DB
        foreach ($yml as $setting) {
            if (trim($setting['key']) === '') {
                continue;
            }

            $id = Setting::formatKey($setting['key']);
            $row = $all_settings->firstWhere('id', $id);

            // Doesn't exist in the table, quit early and say there is stuff pending
            if (!$row) {
                Log::info('Setting '.$id.' missing, update available');
                return true;
            }

            // See if any of these column values have changed
            foreach (['name', 'description'] as $column) {
                $currVal = $row->{$column};
                $newVal = $setting[$column];
                if ($currVal !== $newVal) {
                    return true;
                }
            }

            // See if any of the options have changed
            if ($row->type === 'select') {
                if (!empty($row->options) && $row->options !== $setting['options']) {
                    Log::info('Options for '.$id.' changed, update available');
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * See if there are seeds pending for the permissions
     *
     * @return bool
     */
    private function permissionsSeedsPending(): bool
    {
        $all_permissions = DB::table('permissions')->get();

        $data = file_get_contents(database_path('/seeds/permissions.yml'));
        $yml = Yaml::parse($data);

        foreach ($yml as $perm) {
            $row = $all_permissions->firstWhere('name', $perm['name']);
            if (!$row) {
                return true;
            }

            // See if any of these column values have changed
            foreach (['display_name', 'description'] as $column) {
                if ($row->{$column} !== $perm[$column]) {
                    return true;
                }
            }
        }

        return false;
    }
}
