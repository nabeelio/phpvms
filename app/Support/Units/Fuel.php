<?php

namespace App\Support\Units;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Mass
 * @package App\Support\Units
 */
class Fuel extends \PhpUnitsOfMeasure\PhysicalQuantity\Mass implements Arrayable
{
    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.fuel_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * Return value in native unit as integer
     * @return array
     */
    public function toInt()
    {
        return $this->toArray();
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject()
    {
        return [
            'kg' => round($this->toUnit('kg'), 2),
            'lbs' => round($this->toUnit('lbs'), 2),
        ];
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return round($this->toUnit(
            config('phpvms.internal_units.fuel')
        ), 2);
    }
}
