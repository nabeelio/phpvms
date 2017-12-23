<?php

/**
 * Shortcut for retrieving a setting value
 */
if (!function_exists('setting')) {
    function setting($key, $value = null)
    {
        $settingRepo = app('setting');  // defined in AppServiceProvider
        if($value !== null) {
            return $settingRepo->store($key, $value);
        }

        return $settingRepo->retrieve($key);
    }
}

/**
 * Wrap the asset URL in the publicBaseUrl that's been
 * set
 */
if (!function_exists('public_asset')) {
    function public_asset($path, $parameters = [], $secure = null)
    {
        $publicBaseUrl = app()->publicUrlPath();
        if($path[0] !== '/') {
            $path = '/'.$path;
        }

        $path = $publicBaseUrl . $path;

        return url($path, $parameters, $secure);
    }
}
