<?php

namespace App\Interfaces;

use App\Models\Airline;

/**
 * Common functionality used across all of the importers
 * @package App\Interfaces
 */
class ImportExport
{
    public $assetType;
    public $status;

    /**
     * Hold the columns for the particular table
     */
    public static $columns = [];

    /**
     * Get the airline from the ICAO. Create it if it doesn't exist
     * @param $code
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getAirline($code)
    {
        $airline = Airline::firstOrCreate([
            'icao' => $code,
        ], ['name' => $code]);

        return $airline;
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
                if(!$items) {
                    continue;
                }

                $this->kvpToArray($items, $children);
            }

            $ret[$parent] = $children;
        }

        return $ret;
    }

    /**
     * @param $obj
     * @return mixed
     */
    public function objectToMultiString($obj)
    {
        if(!\is_array($obj)) {
            return $obj;
        }

        $ret_list = [];
        foreach ($obj as $key => $val) {
            if(is_numeric($key) && !\is_array($val)) {
                $ret_list[] = $val;
                continue;
            }

            $key = trim($key);

            if(!\is_array($val)) {
                $val = trim($val);
                $ret_list[] = "{$key}={$val}";
            } else {
                $q = [];
                foreach($val as $subkey => $subval) {
                    if(is_numeric($subkey)) {
                        $q[] = $subval;
                    } else {
                        $q[] = "{$subkey}={$subval}";
                    }
                }

                $q = implode('&', $q);
                if(!empty($q)) {
                    $ret_list[] = "{$key}?{$q}";
                } else {
                    $ret_list[] = $key;
                }
            }
        }

        return implode(';', $ret_list);
    }
}
