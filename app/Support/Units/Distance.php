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
     * The unit that this is stored as
     */
    public const STORAGE_UNIT = 'nmi';

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
        return round($this->toUnit(self::STORAGE_UNIT), 2);
    }
}
