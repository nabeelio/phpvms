<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $env = App::environment();
        $path = database_path('seeds/'.$env.'.yml');
        if(!file_exists($path)) {
            $path = database_path('seeds/prod.yml');
        }

        $svc = app('App\Services\DatabaseService');
        $svc->seed_from_yaml_file($path);
    }

}
