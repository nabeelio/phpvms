<?php

if(!defined('LUMEN_START')) {
    define('LUMEN_START', microtime(true));
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Customized container to allow some of the base Laravel
 * configurations to be overridden
 */
class App extends Illuminate\Foundation\Application
{
    public function __construct(string $basePath = null)
    {
        parent::__construct(dirname(__DIR__) . '/');

        $this->loadEnvironmentFrom('.env');
        $this->useDatabasePath($this->basePath . '/app/Database');
        $this->useStoragePath($this->basePath . '/storage');

        $this->bind('path.public', function () {
            return __DIR__ . '/../public';
        });
    }

    /**
     *
     */
    public function bindInterfaces()
    {
        $this->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            App\Http\Kernel::class
        );

        $this->singleton(
            Illuminate\Contracts\Console\Kernel::class,
            App\Console\Kernel::class
        );

        $this->singleton(
            Illuminate\Contracts\Debug\ExceptionHandler::class,
            App\Exceptions\Handler::class
        );
    }

    /**
     * Override paths
     */

    public function configPath($path = '')
    {
        return $this->basePath . DS . 'config' . ($path ? DS . $path : $path);
    }

    public function resourcePath($path = '')
    {
        return $this->basePath . DS . 'resources' . ($path ? DS . $path : $path);
    }
}

$app = new App();
$app->bindInterfaces();

return $app;
