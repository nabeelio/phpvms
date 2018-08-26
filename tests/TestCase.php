<?php

use App\Services\DatabaseService;
use Tests\TestData;

/**
 * Class TestCase
 */
class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    use TestData;

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
        'x-api-key' => 'testadminapikey',
    ];

    /**
     * @throws Exception
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('database:create', ['--reset' => true]);
        Artisan::call('migrate:refresh', ['--env' => 'unittest']);
    }

    /**
     * Creates the application. Required to be implemented
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /**
     * @param $user
     * @param array $headers
     *
     * @return array
     */
    public function headers($user = null, array $headers = []): array
    {
        if ($user !== null) {
            $headers['x-api-key'] = $user->api_key;
            return $headers;
        }

        if ($this->user !== null) {
            $headers['x-api-key'] = $this->user->api_key;
        }

        return $headers;
    }

    /**
     * Import data from a YML file
     *
     * @param $file
     */
    public function addData($file)
    {
        $svc = app(DatabaseService::class);
        $file_path = base_path('tests/data/'.$file.'.yml');

        try {
            $svc->seed_from_yaml_file($file_path);
        } catch (Exception $e) {
        }
    }

    /**
     * Make sure an object has the list of keys
     *
     * @param $obj
     * @param array $keys
     */
    public function assertHasKeys($obj, array $keys = []): void
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $obj);
        }
    }

    /**
     * So we can test private/protected methods
     * http://bit.ly/1mr5hMq
     *
     * @param       $object
     * @param       $methodName
     * @param array $parameters
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Transform any data that's passed in. E.g, make sure that any mutator
     * classes (e.g, units) are not passed in as the mutator class
     *
     * @param array $data
     *
     * @return array
     */
    protected function transformData(&$data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $this->transformData($value);
            }

            if (is_subclass_of($value, App\Interfaces\Unit::class)) {
                $data[$key] = $value->__toString();
            }

            if ($value instanceof DateTimeImmutable) {
                $data[$key] = $value->format(DATE_ATOM);
            } elseif ($value instanceof Carbon) {
                $data[$key] = $value->toIso8601ZuluString();
            }
        }

        return $data;
    }

    /**
     * Override the GET call to inject the user API key
     *
     * @param string $uri
     * @param array  $headers
     * @param null   $user
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function get($uri, array $headers = [], $user = null): \Illuminate\Foundation\Testing\TestResponse
    {
        $req = parent::get($uri, $this->headers($user, $headers));
        if ($req->isClientError() || $req->isServerError()) {
            Log::error('GET Error: '.$uri, $req->json());
        }

        return $req;
    }

    /**
     * Override the POST calls to inject the user API key
     *
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @param null   $user
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [], $user = null)
    {
        $data = $this->transformData($data);
        $req = parent::post($uri, $data, $this->headers($user, $headers));
        if ($req->isClientError() || $req->isServerError()) {
            Log::error('POST Error: '.$uri, $req->json());
        }

        return $req;
    }

    /**
     * Override the PUT calls to inject the user API key
     *
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @param null   $user
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function put($uri, array $data = [], array $headers = [], $user = null)
    {
        $req = parent::put($uri, $this->transformData($data), $this->headers($user, $headers));
        if ($req->isClientError() || $req->isServerError()) {
            Log::error('PUT Error: '.$uri, $req->json());
        }

        return $req;
    }

    /**
     * Override the DELETE calls to inject the user API key
     *
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     * @param null   $user
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [], $user = null)
    {
        $req = parent::delete($uri, $this->transformData($data), $this->headers($user, $headers));
        if ($req->isClientError() || $req->isServerError()) {
            Log::error('DELETE Error: '.$uri, $req->json());
        }

        return $req;
    }
}
