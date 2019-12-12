<?php

namespace App\Support;

use Illuminate\Contracts\Container\BindingResolutionException;
use Nwidart\Modules\Facades\Module;

/**
 * Global utilities
 */
class Utils
{
    /**
     * Enable the debug toolbar
     */
    public static function enableDebugToolbar()
    {
        try {
            app('debugbar')->enable();
        } catch (BindingResolutionException $e) {
        }
    }

    /**
     * Disable the debug toolbar
     */
    public static function disableDebugToolbar()
    {
        try {
            app('debugbar')->disable();
        } catch (BindingResolutionException $e) {
        }
    }

    /**
     * Is the installer enabled?
     *
     * @return bool
     */
    public static function installerEnabled()
    {
        /** @var \Nwidart\Modules\Module $installer */
        $installer = Module::find('installer');
        if (!$installer) {
            return false;
        }

        return $installer->isEnabled();
    }
}
