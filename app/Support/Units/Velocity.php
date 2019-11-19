<?php

namespace App\Support\Units;

use App\Contracts\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Velocity as VelocityUnit;

/**
 * Class Velocity
 */
class Velocity extends Unit
{
    public $responseUnits = [
        'km/h',
        'knots',
    ];

    /**
     * @param float  $value
     * @param string $unit
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct($value, string $unit)
    {
        if (empty($value)) {
            $value = 0;
        }

        $this->unit = setting('units.speed');
        $this->instance = new VelocityUnit($value, $unit);
    }
}
