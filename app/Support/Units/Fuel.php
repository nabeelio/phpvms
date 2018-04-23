<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;

/**
 * @package App\Support\Units
 */
class Fuel extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.fuel');
        $this->instance = new Mass($value, $unit);

        $this->units = [
            'kg'  => round($this->instance->toUnit('kg'), 2),
            'lbs' => round($this->instance->toUnit('lbs'), 2),
        ];
    }
}
