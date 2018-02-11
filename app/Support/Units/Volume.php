<?php

namespace App\Support\Units;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Volume extends \PhpUnitsOfMeasure\PhysicalQuantity\Volume
{
    /**
     * The unit that this is stored as
     */
    public const STORAGE_UNIT = 'gal';

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
     * For the HTTP Resource call
     */
    public function toJson()
    {
        return [
            'gal'     => round($this->toUnit('gal'), 2),
            'liters'  => round($this->toUnit('liters'), 2),
        ];
    }
}
