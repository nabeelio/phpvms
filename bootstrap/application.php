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
    private $publicDirPath,
            $publicUrlPath = '/';

    public function __construct(string $basePath = null)
    {
        parent::__construct(dirname(__DIR__) . '/');

        $this->loadEnvironmentFrom('env.php');
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

    public function setPublicPath($publicDirPath)
    {
        $this->publicDirPath = $publicDirPath;
    }

    /**
     * Added for the custom filesystem driver. Used in the index.php
     * in the root of the install to set it to point to /public,
     * instead of just /
     *
     * @param $publicUrlPath
     */
    public function setPublicUrlPath($publicUrlPath)
    {
        $this->publicUrlPath = $publicUrlPath;
    }

    /**
     * Added for the custom filesystem driver lookup on what to use
     * for the base URL
     *
     * @return string
     */
    public function publicUrlPath()
    {
        return $this->publicUrlPath ?: '/';
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
        return $this->publicDirPath ?: $this->basePath . DS . 'public';
    }

    public function resourcePath($path = '')
    {
        return $this->basePath . DS . 'resources' . ($path ? DS . $path : $path);
    }
}
