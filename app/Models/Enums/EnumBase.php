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

    /**
     * @param $value
     * @return mixed
     */
    public static function label($value) {
        return self::$labels[$value];
    }
}
