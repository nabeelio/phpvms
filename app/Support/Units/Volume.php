<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume as VolumeUnit;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Volume extends Unit
{
    /**
     * @param float  $value
     * @param string $unit
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.volume');
        $this->instance = new VolumeUnit($value, $unit);

        $this->units = [
            'gal'    => round($this->instance->toUnit('gal'), 2),
            'liters' => round($this->instance->toUnit('liters'), 2),
        ];
    }
}
