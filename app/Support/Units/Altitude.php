<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;

class Altitude extends Unit
{
    public $responseUnits = [
        'ft',
        'km',
        'm',
    ];

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
    }
}
