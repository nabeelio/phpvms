<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class LoadConfiguration
 * @package App\Bootstrap
 *
 * I'm overriding this to take advantage of the configuration caching
 * and not needing to read the files from disk every time.
 *
 * Hopefully it won't affect anything within core framework but this
 * should be ok. Will just have to be cognizant of any changes to the
 * LoadConfiguration parent class, or if the Kernel changes the boot
 * order -NS
 */
class LoadConfiguration extends \Illuminate\Foundation\Bootstrap\LoadConfiguration
{
    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Contracts\Config\Repository $repository
     * @return void
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository)
    {
        parent::loadConfigurationFiles($app, $repository);

        /**
         * Read in the base config, only if it exists
         */
        if (file_exists($app->basePath().'/config.php')) {
            $local_conf = include $app->basePath().'/config.php';
            foreach ($local_conf as $namespace => $override_config) {
                $config = $repository->get($namespace, []);
                $update_config = array_replace_recursive($config, $override_config);
                $repository->set($namespace, $update_config);
            }
        }
    }
}
