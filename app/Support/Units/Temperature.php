<?php

namespace App\Support\Units;

use App\Contracts\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Temperature as TemperatureUnit;

/**
 * Composition for the converter
 */
class Temperature extends Unit
{
    public array $responseUnits = [
        'C',
        'F',
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

        $this->localUnit = setting('units.temperature');
        $this->internalUnit = config('phpvms.internal_units.temperature');

        if ($value instanceof self) {
            $value->toUnit($unit);
            $this->instance = $value->instance;
        } else {
            $this->instance = new TemperatureUnit($value, $unit);
        }
    }
}
