<?php

namespace App\Support;

use Nwidart\Modules\Facades\Module;

/**
 * Global utilities
 *
 * @package App\Support
 */
class Utils
{
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
