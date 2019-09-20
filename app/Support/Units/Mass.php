<?php

namespace App\Support\Units;

use App\Contracts\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass as MassUnit;

class Mass extends Unit
{
    public $responseUnits = [
        'kg',
        'lbs',
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

        $this->unit = setting('units.weight');
        $this->instance = new MassUnit($value, $unit);
    }
}
