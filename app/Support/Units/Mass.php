<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass as MassUnit;

/**
 * @package App\Support\Units
 */
class Mass extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.weight');
        $this->instance = new MassUnit($value, $unit);

        $this->units = [
            'kg'  => round($this->instance->toUnit('kg'), 2),
            'lbs' => round($this->instance->toUnit('lbs'), 2),
        ];
    }
}
