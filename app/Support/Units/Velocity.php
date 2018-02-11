<?php

namespace App\Support\Units;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Velocity
 * @package App\Support\Units
 */
class Velocity extends \PhpUnitsOfMeasure\PhysicalQuantity\Velocity implements Arrayable
{
    /**
     * The unit that this is stored as
     */
    public const STORAGE_UNIT = 'knot';

    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.speed_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject()
    {
        return [
            'knot' => round($this->toUnit('knot'), 2),
            'km/h' => round($this->toUnit('km/h'), 2),
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
