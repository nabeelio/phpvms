<?php

namespace App\Support\Units;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Mass
 * @package App\Support\Units
 */
class Mass extends \PhpUnitsOfMeasure\PhysicalQuantity\Mass implements Arrayable
{
    /**
     * The unit this is stored as
     */
    public const STORAGE_UNIT = 'lbs';

    /**
     * @return string
     */
    public function __toString()
    {
        $unit = setting('general.weight_unit');
        $value = $this->toUnit($unit);
        return (string) round($value, 2);
    }

    /**
     * For the HTTP Resource call
     */
    public function toObject()
    {
        return [
            'kg' => round($this->toUnit('kg'), 2),
            'lbs' => round($this->toUnit('lbs'), 2),
        ];
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return round($this->toUnit(self::STORAGE_UNIT), 2);
    }
}
