<?php

namespace App\Models\Enums;

/**
 * Class EnumBase
 * @package App\Models\Enums
 */
abstract class Enum
{
    protected static $labels = [];
    protected static $cache = [];

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

    /**
     * Returns all possible values as an array
     * @return array Constant name in key, constant value in value
     * @throws \ReflectionException
     */
    public static function toArray()
    {
        $class = get_called_class();
        if (!array_key_exists($class, static::$cache)) {
            $reflection = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        return static::$cache[$class];
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     * @param string $name
     * @param array $arguments
     * @return static
     * @throws \BadMethodCallException
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($array[$name]);
        }
        throw new \BadMethodCallException(
            "No static method or enum constant '$name' in class ".get_called_class()
        );
    }
}
