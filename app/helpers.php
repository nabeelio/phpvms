<?php

use App\Exceptions\SettingNotFound;
use App\Repositories\SettingRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;

/*
 * array_key_first only exists in PHP 7.3+
 */
if (!function_exists('array_key_first')) {
    function array_key_first(array $arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }
    }
}

if (!function_exists('in_mask')) {
    /**
     * Return true/false if a value exists in a mask
     *
     * @param $mask
     * @param $value
     *
     * @return bool
     */
    function in_mask($mask, $value)
    {
        return ($mask & $value) === $value;
    }
}

if (!function_exists('get_truth_state')) {
    /**
     * Check if the passed state matches any of the states that
     * we regard as being true or false
     *
     * @param $state
     *
     * @return bool
     */
    function get_truth_state($state)
    {
        $enabledStates = [
            'yes',
            'y',
            'on',
            'true',
            '1',
            true,
        ];

        if (is_string($state)) {
            $state = strtolower($state);
        }

        return in_array($state, $enabledStates, false);
    }
}

if (!function_exists('list_to_assoc')) {
    /**
     * Converts a straight list into an assoc array with
     * key and value being the same. Mainly for a select box
     *
     * e.g.:
     *    [ 0 => 'item1', 1 => 'item2']
     * to:
     *    ['item1' => 'item1', 'item2' => 'item2']
     *
     * @param array $list
     *
     * @return array
     */
    function list_to_assoc(array $list)
    {
        $ret = [];
        foreach ($list as $item) {
            if (substr_count($item, '=')) {
                [$item, $title] = explode('=', $item);
            } else {
                $title = $item;
            }

            $ret[$item] = $title;
        }

        return $ret;
    }
}

if (!function_exists('list_to_editable')) {
    /**
     * Convert a list (select box) into an editable list
     * https://vitalets.github.io/x-editable/docs.html#select
     * Takes a list of:
     *    [value => text, valueN => textN, ...]
     * Return:
     *    [{value: 1, text: "text1"}, {value: 2, text: "text2"}, ...]
     *
     * @param array $list
     *
     * @return array
     */
    function list_to_editable(array $list)
    {
        $editable = [];
        foreach ($list as $value => $key) {
            $editable[] = [
                'text'  => $key,
                'value' => $value,
            ];
        }

        return $editable;
    }
}

if (!function_exists('skin_view')) {
    /**
     * Render a skin
     *
     * @param       $template
     * @param array $vars
     * @param array $merge_data
     *
     * @return Factory|\Illuminate\View\View
     */
    function skin_view($template, array $vars = [], array $merge_data = [])
    {
        // Add the current skin name so we don't need to hardcode it in the templates
        // Makes it a bit easier to create a new skin by modifying an existing one
        if (View::exists($template)) {
            return view($template, $vars, $merge_data);
        }

        $tpl = 'layouts/'.setting('general.theme', 'default').'/'.$template;

        return view($tpl, $vars, $merge_data);
    }
}

/*
 * Shortcut for retrieving a setting value
 */
if (!function_exists('setting')) {
    /**
     * Read a setting from the settings table
     *
     * @param       $key
     * @param mixed $default
     *
     * @throws \Exception
     *
     * @return mixed|null
     */
    function setting($key, $default = null)
    {
        $settingRepo = app(SettingRepository::class);

        try {
            $value = $settingRepo->retrieve($key);
        } catch (SettingNotFound $e) {
            return $default;
        } catch (Exception $e) {
            return $default;
        }

        return $value;
    }
}

/*
 * Shortcut for retrieving a setting value
 */
if (!function_exists('setting_save')) {
    function setting_save($key, $value)
    {
        $settingRepo = app('setting');
        $settingRepo->save($key, $value);
        return $value;
    }
}

/*
 * Wrap the asset URL in the publicBaseUrl that's been
 * set
 */
if (!function_exists('public_asset')) {
    function public_asset($path, array $parameters = [])
    {
        $publicBaseUrl = app()->publicUrlPath();
        $path = $publicBaseUrl.$path;

        $path = str_replace('//', '/', $path);

        return url($path, $parameters);
    }
}

/*
 * Call mix() and then prepend the proper public URL
 */
if (!function_exists('public_mix')) {
    function public_mix($path, array $parameters = [])
    {
        try {
            $path = mix($path);
        } catch (Exception $e) {
        }

        return public_asset($path, $parameters);
    }
}

/**
 * Wrap a call to url() and append the public folder before it
 */
if (!function_exists('public_url')) {
    function public_url($path, array $parameters = [])
    {
        $publicBaseUrl = app()->publicUrlPath();
        $path = $publicBaseUrl.$path;

        $path = str_replace('//', '/', $path);

        return url($path, $parameters);
    }
}

/*
 * Show a date/time in the proper timezone for a user
 */
if (!function_exists('show_datetime')) {
    /**
     * Format the a Carbon date into the datetime string
     * but convert it into the user's timezone
     *
     * @param Carbon $date
     *
     * @return string
     */
    function show_datetime(Carbon $date = null)
    {
        if ($date === null) {
            return '-';
        }

        $timezone = 'UTC';
        if (Auth::check()) {
            $timezone = Auth::user()->timezone ?: $timezone;
        }

        return $date->timezone($timezone)->toDayDateTimeString();
    }
}

/*
 * Show a date/time in the proper timezone for a user
 */
if (!function_exists('show_date')) {
    /**
     * Format the a Carbon date into the datetime string
     * but convert it into the user's timezone
     *
     * @param \Carbon\Carbon $date
     *
     * @return string
     */
    function show_date(Carbon $date)
    {
        $timezone = 'UTC';
        if (Auth::check()) {
            $timezone = Auth::user()->timezone ?: $timezone;
        }

        return $date->timezone($timezone)->toFormattedDateString();
    }
}

if (!function_exists('_fmt')) {
    /**
     * Replace strings
     *
     * @param       $line    "Hi, my name is :name"
     * @param array $replace ['name' => 'Nabeel']
     *
     * @return mixed
     */
    function _fmt($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        foreach ($replace as $key => $value) {
            $key = strtolower($key);
            $line = str_replace(
                [':'.$key],
                [$value],
                $line
            );
        }

        return $line;
    }
}
