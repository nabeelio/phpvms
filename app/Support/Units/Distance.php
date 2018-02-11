<?php

namespace App\Support\Units;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Distance extends \PhpUnitsOfMeasure\PhysicalQuantity\Length
{
    /**
     * The unit that this is stored as
     */
    public const STORAGE_UNIT = 'mi';

    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.distance_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toJson()
    {
        return [
            'mi'  => round($this->toUnit('miles'), 2),
            'nmi' => round($this->toUnit('nmi'), 2),
            'km'  => round($this->toUnit('meters') / 1000, 2),
        ];
    }
}
