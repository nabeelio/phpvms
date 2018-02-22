<?php

namespace App\Services;

use App\Repositories\FlightRepository;

/**
 * Class ImporterService
 * @package App\Services
 */
class ImporterService extends BaseService
{
    protected $flightRepo;

    public function __construct(
        FlightRepository $flightRepo
    ) {
        $this->flightRepo = $flightRepo;
    }

    /**
     * Set a key-value pair to an array
     * @param $kvp_str
     * @param array $arr
     */
    protected function setKvp($kvp_str, array &$arr)
    {
        $item = explode('=', $kvp_str);
        if(\count($item) === 1) {  # just a list?
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
     * @return array
     */
    public function parseMultiColumnValues($field): array
    {
        $ret = [];
        $split_values = explode(';', $field);
        foreach($split_values as $value) {

            # This isn't in the query string format, so it's
            # just a straight key-value pair set
            if(strpos($value, '?') === false) {
                $this->setKvp($value, $ret);
                continue;
            }

            # This contains the query string, which turns it
            # into the multi-level array

            $query_str = explode('?', $value);
            $parent = trim($query_str[0]);

            $children = [];
            $kvp = explode('&', trim($query_str[1]));
            foreach($kvp as $items) {
                $this->setKvp($items, $children);
            }

            $ret[$parent] = $children;
        }

        return $ret;
    }

    /**
     * Import flights
     * @param $csv_str
     */
    public function importFlights($csv_str)
    {

    }
}
