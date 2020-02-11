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
     * Returns a 40 character API key that a user can use
     *
     * @return string
     */
    public static function generateApiKey(): string
    {
        $key = substr(sha1(time().mt_rand()), 0, 20);
        return $key;
    }

    /**
     * Simple check on the first character if it's an object or not
     *
     * @param $obj
     *
     * @return bool
     */
    public static function isObject($obj): bool
    {
        if (!$obj) {
            return false;
        }

        if ($obj[0] === '{' || $obj[0] === '[') {
            return true;
        }

        return false;
    }

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
