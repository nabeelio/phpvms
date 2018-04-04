<?php

namespace App\Support;

use MetarDecoder\MetarDecoder;

/**
 * Wrapper around the METAR decoder. Compensate for
 * errors and have tests around this functionality
 * @package App\Support
 */
class Metar
{
    private $metar,
            $metar_str;

    /**
     * Metar constructor.
     * @param $metar_str
     */
    public function __construct($metar_str)
    {
        $decoder = new MetarDecoder();
        $this->metar = $decoder->parse($metar_str);
        $this->metar_str = $metar_str;
    }

    /**
     * Return if this is VFR or IFR conditions
     * @return string
     */
    public function getCategory(): string
    {
        $category = 'VFR';

        $visibility = $this->getVisibility(false);
        $ceiling = $this->getCeiling(false);

        if ($visibility < 3 || $ceiling < 1000) {
            $category = 'IFR';
        }

        return $category;
    }

    /**
     * Return the ceiling
     * @param bool $convert
     * @return int
     */
    public function getCeiling($convert = true): int
    {
        $ceiling = 1000;
        $clouds = $this->metar->getClouds();
        if ($clouds && \count($clouds) > 0) {
            $ceiling = $clouds[0]->getBaseHeight()->getValue();
        }

        if(!$convert) {
            return $ceiling;
        }

        return $ceiling;
    }

    /**
     * Return all of the cloud layers
     */
    public function getClouds(): array
    {
        if (!$this->metar->getClouds()) {
            return [];
        }

        $layers = [];
        $unit = setting('units.altitude');

        foreach($this->metar->getClouds() as $cloud) {
            if($unit === 'ft') {
                $base_height = $cloud->getBaseHeight()->getValue();
            } else {
                $base_height = $cloud->getBaseHeight()->getConvertedValue('m');
            }

            $layers[] = [
                'amount' => $cloud->getAmount(),
                'base_height' => $base_height,

            ];
        }

        return $layers;
    }

    /**
     * Last update time
     * @return string
     */
    public function getLastUpdate(): string
    {
        return $this->metar->getTime();
    }

    /**
     * Get the pressure, pass in the unit type
     * @param string $unit Pass mb for millibars, hg for hg
     * @return float|null
     */
    public function getPressure($unit = 'mb')
    {
        if (!$this->metar->getPressure()) {
            return null;
        }

        $pressure = $this->metar->getPressure()->getValue();
        if (strtolower($unit) === 'mb') {
            return $pressure;
        }

        return round($pressure * 33.86, 2);
    }

    /**
     * Return the raw metar string
     * @return mixed
     */
    public function getRawMetar()
    {
        return $this->metar_str;
    }

    /**
     * Return the temperature, if it exists in the METAR
     * Convert to the units that are set in the VA
     * @return float|null
     */
    public function getTemperature()
    {
        if (!$this->metar->getAirTemperature()) {
            return null;
        }

        if(setting('units.temperature') === 'c') {
            return $this->metar->getAirTemperature()->getValue();
        }

        // Convert to F
        round(($this->metar->getAirTemperature()->getValue() * 9 / 5) + 32, 2);
    }

    /**
     * Get the visibility
     * @param bool $convert
     * @return int
     */
    public function getVisibility($convert=true): int
    {
        // initially in miles
        $visibility = 10; // assume it's ok and VFR
        $vis = $this->metar->getVisibility();
        if ($vis) {
            $vis = $vis->getVisibility();
            if ($vis) {
                $visibility = (int) $vis->getValue();

                if ($convert && setting('units.distance') === 'km') {
                    return $vis->getConvertedValue('m') / 1000;
                }

                return $visibility;
            }
        }

        if($convert && setting('units.distance') === 'km') {
            return round($visibility * 1.60934, 2);
        }

        return $visibility;
    }

    /**
     * Return wind information
     */
    public function getWinds()
    {
        $sw = $this->metar->getSurfaceWind();
        if (!$sw) {
            return null;
        }

        $ret = [
            'speed' => null,
            'direction' => null,
            'gusts' => null,
        ];

        $mean_speed = $sw->getMeanSpeed();
        if($mean_speed) {
            $ret['speed'] = $mean_speed->getConvertedValue('kt');
        }

        $dir = $sw->getMeanDirection();
        if($dir) {
            $ret['direction'] = $dir->getValue();
        }

        $gusts = $sw->getSpeedVariations();
        if($gusts) {
            $ret['gusts'] = $gusts->getConvertedValue('kt');
        }

        return $ret;
    }
}
