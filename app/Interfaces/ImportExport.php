<?php

namespace App\Interfaces;

use App\Models\Airline;

/**
 * Common functionality used across all of the importers
 * @package App\Interfaces
 */
class ImportExport
{
    public $status;

    /**
     * Hold the columns for the particular table
     */
    public static $columns = [];

    /**
     * Need to implement in a child class!
     * @throws \RuntimeException
     */
    public function export()
    {
        throw new \RuntimeException('Calling export, needs to be implemented in child!');
    }

    /**
     * Need to implement in a child class!
     * @param array $row
     * @param       $index
     * @throws \RuntimeException
     */
    public function import(array $row, $index)
    {
        throw new \RuntimeException('Calling import, needs to be implemented in child!');
    }

    /**
     * Get the airline from the ICAO
     * @param $code
     * @return \App\Models\Airline
     */
    public function getAirline($code)
    {
        return Airline::where('icao', $code)->first();
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return static::$columns;
    }

    /**
     * Set a key-value pair to an array
     * @param       $kvp_str
     * @param array $arr
     */
    protected function kvpToArray($kvp_str, array &$arr)
    {
        $item = explode('=', $kvp_str);
        if (\count($item) === 1) {  # just a list?
            $arr[] = trim($item[0]);
        } else {  # actually a key-value pair
            $k = trim($item[0]);
            $v = trim($item[1]);
            $arr[$k] = $v;
        }
    }

    /**
     * Parse a multi column values field. E.g:
     * Y?price=200&cost=100; F?price=1200
     *    or
     * gate=B32;cost index=100
     *
     * Converted into a multi-dimensional array
     *
     * @param $field
     * @return array|string
     */
    public function parseMultiColumnValues($field)
    {
        $ret = [];
        $split_values = explode(';', $field);

        # No multiple values in here, just a straight value
        if (\count($split_values) === 1) {
            return [$split_values[0]];
        }

        foreach ($split_values as $value) {
            # This isn't in the query string format, so it's
            # just a straight key-value pair set
            if (strpos($value, '?') === false) {
                $this->kvpToArray($value, $ret);
                continue;
            }

            # This contains the query string, which turns it
            # into the multi-level array

            $query_str = explode('?', $value);
            $parent = trim($query_str[0]);

            $children = [];
            $kvp = explode('&', trim($query_str[1]));
            foreach ($kvp as $items) {
                $this->kvpToArray($items, $children);
            }

            $ret[$parent] = $children;
        }

        return $ret;
    }
}
