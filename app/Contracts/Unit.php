<?php

namespace App\Contracts;

use ArrayAccess;
use PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure;

/**
 * Abstract unit wrapper
 */
class Unit implements ArrayAccess
{
    /**
     * The unit this is kept as
     */
    public $unit;

    /**
     * All of the units of this class
     */
    public $units;

    /**
     * Holds an instance of the PhpUnit type
     *
     * @var \PhpUnitsOfMeasure\AbstractPhysicalQuantity
     */
    protected $instance;

    /**
     * Units that are included as part of the REST response
     */
    public $responseUnits = [];

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->__toString();
    }

    /**
     * Just call toUnit() on the PhpUnitOfMeasure instance
     *
     * @param string $unit
     *
     * @return mixed
     */
    public function toUnit($unit)
    {
        return $this->instance->toUnit($unit);
    }

    /**
     * Return all of the units that get sent back in a response
     */
    public function getResponseUnits(): array
    {
        $response = [];
        foreach ($this->responseUnits as $unit) {
            $response[$unit] = $this[$unit] ?? 0;
        }

        return $response;
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
        return $this->offsetGet($offset) !== null;
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
        try {
            $value = $this->instance->toUnit($offset);
            if (!$value) {
                return;
            }
        } catch (UnknownUnitOfMeasure $e) {
            return;
        }

        return round($value, 2);
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        // $this->units[$offset] = $value;
    }

    /**
     * Implements ArrayAccess
     *
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        // $this->units[$offset] = null;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return (string) $this->offsetGet($this->unit);
    }
}
