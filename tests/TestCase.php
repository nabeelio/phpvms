<?php

use App\Models\User;
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
    public static $prefix = '/api';

    protected $app;
    protected $baseUrl = 'http://localhost';
    protected $connectionsToTransact = ['testing'];

    protected $user;

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
            'x-api-key' => $user->api_key,
        ];
    }

    /**
     * Return the URL with the URI prefix
     * @param $uri
     * @return string
     */
    public function u($uri) {
        return self::$prefix . $uri;
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
     * Override the GET call to inject the user API key
     * @param string $uri
     * @param array $headers
     * @param null $user
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function get($uri, array $headers=[], $user=null): \Illuminate\Foundation\Testing\TestResponse
    {
        if($this->user !== null) {
            $headers = $this->headers($this->user);
        }

        if($user !== null) {
            $headers['x-api-key'] = $user->api_key;
        }

        return parent::get($uri, $headers);
    }

    /**
     * Override the POST calls to inject the user API key
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        if (empty($headers)) {
            if ($this->user !== null) {
                $headers = $this->headers($this->user);
            }
        }

        return parent::post($uri, $data, $headers);
    }

    /**
     * Override the DELETE calls to inject the user API key
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        if (empty($headers)) {
            if ($this->user !== null) {
                $headers = $this->headers($this->user);
            }
        }

        return parent::delete($uri, $data, $headers);
    }
}
