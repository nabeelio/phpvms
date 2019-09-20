<?php

namespace App\Support\Units;

use App\Contracts\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume as VolumeUnit;

/**
 * Wrap the converter class
 */
class Volume extends Unit
{
    public $responseUnits = [
        'gal',
        'liters',
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

        $this->unit = setting('units.volume');
        $this->instance = new VolumeUnit($value, $unit);
    }
}
