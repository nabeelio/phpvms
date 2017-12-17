<?php

use Illuminate\Foundation\Application as LaravelApplication;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Customized container to allow some of the base Laravel
 * configurations to be overridden
 */
class Application extends LaravelApplication
{
    private $publicPath;

    public function __construct(string $basePath = null)
    {
        parent::__construct(dirname(__DIR__) . '/');

        $this->loadEnvironmentFrom('.env');
        $this->useDatabasePath($this->basePath . '/app/Database');
        $this->useStoragePath($this->basePath . '/storage');
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

    public function setPublicPath($publicPath)
    {
        $this->publicPath = $publicPath;
    }

    public function configPath($path = '')
    {
        return $this->basePath . DS . 'config' . ($path ? DS . $path : $path);
    }

    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath;
    }

    public function langPath()
    {
        return $this->resourcePath() . DS . 'lang';
    }

    public function publicPath()
    {
        return $this->publicPath ?: $this->basePath . DS . 'public';
    }

    public function resourcePath($path = '')
    {
        return $this->basePath . DS . 'resources' . ($path ? DS . $path : $path);
    }
}
