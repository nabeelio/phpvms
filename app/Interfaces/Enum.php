<?php

namespace App\Interfaces;

/**
 * Borrowed some ideas from myclabs/php-enum after this was created
 */
abstract class Enum
{
    protected static $cache = [];
    protected static $codes = [];
    protected static $labels = [];

    /**
     * @var int
     */
    protected $value;

    /**
     * Create an instance of this Enum
     *
     * @param $val
     */
    public function __construct($val)
    {
        $this->value = $val;
    }

    /**
     * Return the value that's been set if this is an instance
     *
     * @return mixed
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the label, try to return the translated version as well
     *
     * @param $value
     *
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
     * Get the numeric value from a string code
     *
     * @param $code
     *
     * @return mixed|null
     */
    public static function getFromCode($code)
    {
        return array_search($code, static::$codes, true);
    }

    /**
     * Convert the integer value into one of the codes
     *
     * @param $value
     *
     * @return false|int|string
     */
    public static function convertToCode($value)
    {
        $value = (int) $value;
        if (!array_key_exists($value, static::$codes)) {
            return;
        }

        return static::$codes[$value];
    }

    /**
     * Select box entry items
     *
     * @param bool $add_blank
     *
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
     *
     * @throws \ReflectionException
     *
     * @return array Constant name in key, constant value in value
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
     *
     * @return bool
     */
    final public function equals(self $enum): bool
    {
        return $this->getValue() === $enum->getValue() && static::class === \get_class($enum);
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a
     * class constant
     *
     * @param string $name
     * @param array  $arguments
     *
     * @throws \BadMethodCallException
     * @throws \ReflectionException
     *
     * @return static
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($array[$name]);
        }

        throw new \BadMethodCallException(
            "No static method or enum constant '$name' in class ".static::class
        );
    }
}
