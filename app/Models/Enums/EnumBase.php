<?php
/**
 * Created by IntelliJ IDEA.
 * User: nshahzad
 * Date: 12/19/17
 * Time: 8:04 PM
 */

namespace App\Models\Enums;


/**
 * Class EnumBase
 * @package App\Models\Enums
 * TODO: Implement lang translations for enum labels
 */
class EnumBase
{
    protected static $labels = [];
    protected static $adverbs = [];

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
}
