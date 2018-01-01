<?php

namespace App\Models\Enums;

/**
 * Class EnumBase
 * @package App\Models\Enums
 */
class EnumBase
{
    protected static $labels = [];

    /**
     * Return the label, try to return the translated version as well
     * @param $value
     * @return mixed
     */
    public static function label($value) {
        if(isset(static::$labels[$value])) {
            return trans(static::$labels[$value]);
        }
    }

    /**
     * Return all of the (translated) labels
     */
    public static function labels()
    {
        $labels = [];
        foreach(static::$labels as $key => $label) {
            $labels[$key] = trans($label);
        }

        return $labels;
    }

    /**
     * Select box
     */
    public static function select($add_blank=false)
    {
        $labels = [];
        if($add_blank) {
            $labels[] = '';
        }

        foreach (static::$labels as $key => $label) {
            $labels[$key] = trans($label);
        }

        return $labels;
    }
}
