<?php

namespace App\Support;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
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

    /**
     * Get the domain from a URL
     *
     * @param string $url
     *
     * @return string
     */
    public static function getRootDomain(string $url): string
    {
        if (!Str::contains($url, ['https://', 'http://'])) {
            $url = 'http://'.$url;
        }

        $domain = parse_url($url, PHP_URL_HOST);
        $domain = explode('.', $domain);
        $len = count($domain);
        if ($len == 1) {
            return $domain[0];
        }

        $domain = $domain[$len - 2].'.'.$domain[$len - 1];

        return $domain;
    }
}
