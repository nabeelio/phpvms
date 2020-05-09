<?php

namespace App\Support;

use App\Contracts\Model;
use Hashids\Hashids;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use LayerShifter\TLDExtract\Extract;
use Nwidart\Modules\Facades\Module;

/**
 * Global utilities
 */
class Utils
{
    /**
     * Generate a new ID with a given length
     *
     * @param int [$length]
     *
     * @return string
     */
    public static function generateNewId(int $length = null)
    {
        if (!$length) {
            $length = Model::ID_MAX_LENGTH;
        }

        $hashids = new Hashids(uniqid(), $length);
        $mt = str_replace('.', '', microtime(true));
        return $hashids->encode($mt);
    }

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
        if (Str::contains($url, ['https://', 'http://'])) {
            $url = str_replace('https://', '', $url);
            $url = str_replace('http://', '', $url);
        }

        $extract = new Extract();
        $result = $extract->parse($url);

        return $result->getRegistrableDomain();
    }
}
