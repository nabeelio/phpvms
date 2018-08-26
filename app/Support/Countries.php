<?php

namespace App\Support;

/**
 * Class Countries
 */
class Countries
{
    /**
     * Get a select box list of all the countries
     *
     * @return static
     */
    public static function getSelectList()
    {
        $countries = collect((new \League\ISO3166\ISO3166())->all())
            ->mapWithKeys(function ($item, $key) {
                return [strtolower($item['alpha2']) => $item['name']];
            });

        return $countries;
    }
}
