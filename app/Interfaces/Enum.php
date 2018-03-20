<?php

namespace App\Interfaces;

/**
 * Borrowed some ideas from myclabs/php-enum after this was created
 * @package App\Models\Enums
 */
abstract class Enum
{
    protected static $labels = [];
    protected static $cache = [];

    /**
     * @var integer
     */
    protected $value;

    /**
     * Create an instance of this Enum
     * @param $val
     */
    public function __construct($val)
    {
        $this->value = $val;
    }

    /**
     * Return the value that's been set if this is an instance
     * @return mixed
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the label, try to return the translated version as well
     * @param $value
     * @return mixed
     */
    final public static function label($value)
    {
        if (isset(static::$labels[$value])) {
            return trans(static::$labels[$value]);
        }
    }

    /**
     * Return all of the (translated) labels
     */
    final public static function labels(): array
    {
        $labels = [];
        foreach (static::$labels as $key => $label) {
            $labels[$key] = trans($label);
        }

        return $labels;
    }

    /**
     * Select box entry items
     * @param bool    $add_blank
     * @return array
     */
    public static function select($add_blank = false): array
    {
        $labels = [];
        if ($add_blank) {
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
    public static function toArray(): array
    {
        $class = static::class;
        if (!array_key_exists($class, static::$cache)) {
            $reflection = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        return static::$cache[$class];
    }

    /**
     * @param Enum $enum
     * @return bool
     */
    final public function equals(Enum $enum): bool
    {
        return $this->getValue() === $enum->getValue() && get_called_class() == get_class($enum);
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a
     * class constant
     * @param string $name
     * @param array  $arguments
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
