<?php

namespace App\Support\Units;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Volume extends \PhpUnitsOfMeasure\PhysicalQuantity\Volume implements Arrayable
{
    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.liquid_unit');
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
            'gal'     => round($this->toUnit('gal'), 2),
            'liters'  => round($this->toUnit('liters'), 2),
        ];
    }

    /**
     * Get the instance as an array.
     */
    public function toArray()
    {
        return round($this->toUnit(
            config('phpvms.internal_units.volume')
        ), 2);
    }
}
