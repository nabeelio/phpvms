<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


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

    protected static $auth_headers = [
        'x-api-key' => 'testadminapikey'
    ];

    public function apiHeaders()
    {
        return self::$auth_headers;
    }

    public function headers($user)
    {
        return [
            #'accept' => 'application/json',
            #'content-type' => 'application/json',
            'x-api-key' => $user->api_key,
        ];
    }

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
    }

    protected function reset_db() {
        Artisan::call('database:create', ['--reset' => true]);
        Artisan::call('migrate:refresh', ['--env' => 'unittest']);
    }

    public function setUp() {
        parent::setUp();
        $this->reset_db();
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
        try {
            $svc->seed_from_yaml_file($file_path);
        } catch (Exception $e) {
        }
    }

    public function fillableFields(\Illuminate\Database\Eloquent\Model $model)
    {
        //$klass = new $model();
        return $model->fillable;
    }

    /**
     * Make sure an object has the list of keys
     * @param $obj
     * @param array $keys
     */
    public function assertHasKeys($obj, $keys=[])
    {
        foreach($keys as $key) {
            $this->assertArrayHasKey($key, $obj);
        }
    }

    /**
     * Shortcut for a get call with a user
     * @param \App\Models\User $user
     * @param string $uri
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function user_get($user, $uri)
    {
        return $this->withHeaders($this->headers($user))->get($uri);
    }
}
