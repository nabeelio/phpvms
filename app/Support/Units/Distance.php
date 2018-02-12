<?php

namespace App\Support\Units;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Wrap the converter class
 * @package App\Support\Units
 */
class Distance extends \PhpUnitsOfMeasure\PhysicalQuantity\Length implements Arrayable
{
    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.distance_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject(): array
    {
        return [
            'mi'  => round($this->toUnit('miles'), 2),
            'nmi' => round($this->toUnit('nmi'), 2),
            'km'  => round($this->toUnit('meters') / 1000, 2),
        ];
    }

    /**
     * Get the instance as an array.
     */
    public function toArray()
    {
        return round($this->toUnit(
            config('phpvms.internal_units.distance')
        ), 2);
    }
}
