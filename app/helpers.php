<?php

/**
 * Shortcut for retrieving a setting value
 */
if (!function_exists('setting')) {
    function setting($key, $value = null)
    {
        $settingRepo = app('setting');
        if($value === null) {
            return $settingRepo->get($key);
        } else {
            $settingRepo->set($key, $value);
        }
    }
}
