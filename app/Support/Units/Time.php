<?php

namespace App\Support\Units;

/**
 * Class Time
 * @package App\Support\Units
 */
class Time
{
    public $hours,
           $minutes;

    /**
     * Pass just minutes to figure out how many hours
     * Or both hours and minutes
     * @param $minutes
     * @param $hours
     */
    public function __construct($minutes, $hours=null)
    {
        if(!empty($hours)) {
            $this->hours = $hours;
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
        return $this->getTotalMinutes();
    }

    /**
     * Return a time string
     * @return string
     */
    public function __toString()
    {
        return $this->hours . 'h ' . $this->minutes . 'm';
    }
}
