<?php

if (!function_exists('skin_view')) {
    /**
     * Render a skin
     * @param $template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function skin_view($template, array $vars = [], $merge_data = [])
    {
        $tpl = 'layouts/' . config('phpvms.skin') . '/' . $template;
        return view($tpl, $vars, $merge_data);
    }
}

/**
 * Shortcut for retrieving a setting value
 */
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $settingRepo = app('setting');  // defined in AppServiceProvider
        /*if($value !== null) {
            return $settingRepo->store($key, $value);
        }*/

        return $settingRepo->retrieve($key) ?: $default;
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
        $path = $publicBaseUrl . $path;

        $path = str_replace('//', '/', $path);

        return url($path, $parameters, $secure);
    }
}

/**
 * Show a date/time in the proper timezone for a user
 */
if(!function_exists('show_datetime')) {
    /**
     * Format the a Carbon date into the datetime string
     * but convert it into the user's timezone
     * @param \Carbon\Carbon $date
     * @return string
     */
    function show_datetime(\Carbon\Carbon $date=null)
    {
        if(empty($date)) {
            return '-';
        }

        $timezone = 'UTC';
        if (Auth::check()) {
            $timezone = Auth::user()->timezone ?: $timezone;
        }

        return $date->timezone($timezone)->toDayDateTimeString();
    }
}

/**
 * Show a date/time in the proper timezone for a user
 */
if (!function_exists('show_date')) {
    /**
     * Format the a Carbon date into the datetime string
     * but convert it into the user's timezone
     * @param \Carbon\Carbon $date
     * @return string
     */
    function show_date(\Carbon\Carbon $date)
    {
        $timezone = 'UTC';
        if (Auth::check()) {
            $timezone = Auth::user()->timezone ?: $timezone;
        }

        return $date->timezone($timezone)->toFormattedDateString();
    }
}
