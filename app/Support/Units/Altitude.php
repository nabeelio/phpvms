<?php

namespace App\Support\Units;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Altitude extends \PhpUnitsOfMeasure\PhysicalQuantity\Length
{
    /**
     * The unit that this is stored as
     */
    public const STORAGE_UNIT = 'feet';

    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.altitude_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toJson()
    {
        return [
            'ft'  => round($this->toUnit('feet'), 2),
            'm'   => round($this->toUnit('meters') / 1000, 2),
        ];
    }
}
