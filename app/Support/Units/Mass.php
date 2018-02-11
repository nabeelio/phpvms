<?php

namespace App\Support\Units;

/**
 * Class Mass
 * @package App\Support\Units
 */
class Mass extends \PhpUnitsOfMeasure\PhysicalQuantity\Mass
{
    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.weight_unit');
        $value = $this->toUnit($unit);
        return (string)round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toJson()
    {
        return [
            'kg' => round($this->toUnit('kg'), 2),
            'lgs' => round($this->toUnit('lbs'), 2),
        ];
    }
}
