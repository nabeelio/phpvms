<?php

namespace App\Interfaces;

use ArrayAccess;

/**
 * Class Unit
 *
 * @property mixed  $instance
 * @property string $unit
 * @property array  $units
 */
class Unit implements ArrayAccess
{
    /**
     * The unit this is kept as
     */
    public $unit;

    /**
     * All of the units of this class
     *
     * @var array
     */
    public $units;

    /**
     * Holds an instance of the PhpUnit type
     *
     * @var
     */
    protected $instance;

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->__toString();
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->units);
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->units[$offset];
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        $this->units[$offset] = $value;
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        $this->units[$offset] = null;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return (string) $this->units[$this->unit];
    }
}
