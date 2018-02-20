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
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.speed_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * Return value in native unit as integer
     * @return array
     */
    public function toNumber()
    {
        return $this->toArray();
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject()
    {
        return [
            'knots' => round($this->toUnit('knots'), 2),
            'km/h' => round($this->toUnit('km/h'), 2),
        ];
    }

    /**
     * Get the instance as an array.
     */
    public function toArray()
    {
        return round($this->toUnit(
            config('phpvms.internal_units.velocity')
        ), 2);
    }
}
