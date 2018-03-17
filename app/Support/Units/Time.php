<?php

namespace App\Support\Units;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Time
 * @package App\Support\Units
 */
class Time implements Arrayable
{
    public $hours,
           $minutes;

    /**
     * @param $minutes
     * @param $hours
     * @return static
     */
    public static function init($minutes, $hours)
    {
        return new Time($minutes, $hours);
    }

    /**
     * Pass just minutes to figure out how many hours
     * Or both hours and minutes
     * @param $minutes
     * @param $hours
     */
    public function __construct($minutes, $hours=null)
    {
        $minutes = (int) $minutes;

        if(!empty($hours)) {
            $this->hours = (int) $hours;
        } else {
            $this->hours = floor($minutes / 60);
        }

        $this->minutes = $minutes % 60;
    }

    /**
     * Get the total number minutes, adding up the hours
     * @return float|int
     */
    public function getMinutes()
    {
        return ($this->hours * 60) + $this->minutes;
    }

    /**
     * Alias to getMinutes()
     * @alias getMinutes()
     * @return float|int
     */
    public function asInt()
    {
        return $this->getMinutes();
    }

    /**
     * Return a time string
     * @return string
     */
    public function __toString()
    {
        return $this->hours . 'h ' . $this->minutes . 'm';
    }

    /**
     * @return float|int
     */
    public function toObject()
    {
        return $this->getMinutes();
    }

    /**
     * Get the instance as an array.
     */
    public function toArray()
    {
        return $this->getMinutes();
    }
}
