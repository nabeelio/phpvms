<?php

namespace App\Support\Units;

use App\Interfaces\Unit;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;

class Distance extends Unit
{
    /**
     * Distance constructor.
     *
     * @param float  $value
     * @param string $unit
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function __construct(float $value, string $unit)
    {
        $this->unit = setting('units.distance');
        $this->instance = new Length($value, $unit);

        $this->units = [
            'mi'  => round($this->instance->toUnit('miles'), 2),
            'nmi' => round($this->instance->toUnit('nmi'), 2),
            'm'   => round($this->instance->toUnit('meters'), 2),
            'km'  => round($this->instance->toUnit('meters') / 1000, 2),
        ];
    }
}
