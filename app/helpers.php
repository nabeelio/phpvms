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
