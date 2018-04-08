<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Velocity as VelocityUnit;

/**
 * Class Velocity
 * @package App\Support\Units
 */
class Velocity extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.speed');
        $this->instance = new VelocityUnit($value, $unit);

        $this->units = [
            'knots' => round($this->instance->toUnit('knots'), 2),
            'km/h'  => round($this->instance->toUnit('km/h'), 2),
        ];
    }
}
