<?php

use Carbon\Carbon;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $app;
    protected $baseUrl = 'http://localhost';
    protected $connectionsToTransact = ['testing'];

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
    }

    protected function reset_db(){
        exec('make -f ' . __DIR__ . '/../Makefile unittest-db');
    }

    public function setUp() {
        parent::setUp();
        $this->reset_db();
        /*
        Artisan::call('migrate');
        Artisan::call('db:seed');
        */
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        //$app['config']->set('database.default','testing');
        return $app;
    }

    public function createRepository($repo_name) {
        $app = $this->createApplication();
        return $app->make('App\Repositories\\' . $repo_name);
    }

    public function readYaml($file)
    {
        return Yaml::parse(file_get_contents(base_path('tests/data/' . $file . '.yml')));
    }

    public function addData($file)
    {
        $time_fields = ['created_at', 'updated_at'];
        $curr_time = Carbon::now('UTC')->format('Y-m-d H:i:s');

        $yml = $this->readYaml($file);
        foreach ($yml as $table => $rows) {
            foreach ($rows as $row) {

                # encrypt any password fields
                if (array_key_exists('password', $row)) {
                    $row['password'] = bcrypt($row['password']);
                }

                # if any time fields are == to "now", then insert the right time
                foreach ($time_fields as $tf) {
                    if (array_key_exists($tf, $row) && $row[$tf] === 'now') {
                        $row[$tf] = $curr_time;
                    }
                }

                DB::table($table)->insert($row);
            }
        }
    }
}
