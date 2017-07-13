<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


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

    protected function reset_db() {
//        exec('make -f '.__DIR__.'/../Makefile unittest-db');
    }

    public function setUp() {
        parent::setUp();
        $this->reset_db();

        Mail::fake();

        #Artisan::call('migrate');
        #Artisan::call('db:seed');
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

    public function addData($file)
    {
        $svc = app('\App\Services\DatabaseService');
        $file_path = base_path('tests/data/' . $file . '.yml');
        $svc->seed_from_yaml_file($file_path);
    }
}
