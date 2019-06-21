<?php

use App\Services\DatabaseService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private static $seed_mapper = [
        'local'   => 'dev',
        'qa'      => 'dev',
        'staging' => 'dev',
    ];

    private static $always_seed = [
        'permissions',
    ];

    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run()
    {
        $env = App::environment();
        if (array_key_exists($env, self::$seed_mapper)) {
            $env = self::$seed_mapper[$env];
        }

        Log::info('Seeding from environment '.$env);
        $path = database_path('seeds/'.$env.'.yml');

        if (!file_exists($path)) {
            $path = database_path('seeds/prod.yml');
        }

        $svc = app(DatabaseService::class);
        $svc->seed_from_yaml_file($path);

        // Always seed/sync these
        foreach (self::$always_seed as $file) {
            Log::info('Importing '.$file);
            $path = database_path('seeds/'.$file.'.yml');
            if (file_exists($path)) {
                $svc->seed_from_yaml_file($path);
            }
        }
    }
}
