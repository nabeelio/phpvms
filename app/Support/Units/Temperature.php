<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Temperature as TemperatureUnit;

/**
 * Composition for the converter
 * @package App\Support\Units
 */
class Temperature extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.temperature');
        $this->instance = new TemperatureUnit($value, $unit);

        $this->units = [
            'F' => round($this->instance->toUnit('F'), 2),
            'f' => round($this->instance->toUnit('F'), 2),
            'C' => round($this->instance->toUnit('C'), 2),
            'c' => round($this->instance->toUnit('C'), 2),
        ];
    }
}
