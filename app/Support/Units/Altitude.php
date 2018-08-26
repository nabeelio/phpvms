<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;

class Altitude extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.altitude');
        $this->instance = new Length($value, $unit);

        $this->units = [
            'm'  => round($this->instance->toUnit('meters'), 2),
            'km' => round($this->instance->toUnit('meters') / 1000, 2),
            'ft' => round($this->instance->toUnit('feet'), 2),
        ];
    }
}
