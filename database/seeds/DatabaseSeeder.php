<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Map these other environments to a specific seed file
     * @var array
     */
    public static $seed_mapper = [
        'local' =>      'dev',
        'qa' =>         'dev',
        'staging' =>    'dev',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $env = App::environment();
        if(in_array($env, self::$seed_mapper, true)) {
            $env = self::$seed_mapper[$env];
        }

        $path = database_path('seeds/'.$env.'.yml');
        print("Seeding seeds/$env.yml\n");

        if(!file_exists($path)) {
            $path = database_path('seeds/prod.yml');
        }

        $svc = app('App\Services\DatabaseService');
        $svc->seed_from_yaml_file($path);
    }
}
