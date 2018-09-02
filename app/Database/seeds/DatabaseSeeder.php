<?php

use App\Services\DatabaseService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Map these other environments to a specific seed file
     *
     * @var array
     */
    public static $seed_mapper = [
        'local'   => 'dev',
        'qa'      => 'dev',
        'staging' => 'dev',
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

        $path = database_path('seeds/'.$env.'.yml');

        if (!file_exists($path)) {
            $path = database_path('seeds/prod.yml');
        }

        $svc = app(DatabaseService::class);
        $svc->seed_from_yaml_file($path);
    }
}
