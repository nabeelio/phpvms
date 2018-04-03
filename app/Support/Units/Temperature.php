<?php

namespace App\Support\Units;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Temperature extends \PhpUnitsOfMeasure\PhysicalQuantity\Temperature implements Arrayable
{
    /**
     * @return string
     */
    public function __toString()
    {
        $unit = strtoupper(setting('units.temperature'));
        $value = $this->toUnit($unit);

        return (string) round($value, 2);
    }

    /**
     * Return value in native unit as integer
     * @return array
     */
    public function toNumber()
    {
        return $this->toArray();
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject()
    {
        return [
            'f' => round($this->toUnit('F'), 2),
            'c'  => round($this->toUnit('C') / 1000, 2),
        ];
    }

    /**
     * Get the instance as an array.
     */
    public function toArray()
    {
        return $this->__toString();
    }
}
