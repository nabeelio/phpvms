<?php

namespace App\Support\Units;

use App\Contracts\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Pressure as PressureUnit;

/**
 * Composition for the converter
 */
class Pressure extends Unit
{
    public $responseUnits = [
        'atm',
        'hPa',
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

        $this->unit = setting('units.temperature');
        $this->instance = new PressureUnit($value, $unit);
    }
}
