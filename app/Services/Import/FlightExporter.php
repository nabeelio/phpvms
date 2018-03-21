<?php

namespace App\Services\Import;

use App\Interfaces\ImportExport;
use App\Models\Enums\FlightType;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use Log;

/**
 * The flight importer can be imported or export. Operates on rows
 *
 * @package App\Services\Import
 */
class FlightExporter extends ImportExport
{
    /**
     * Set the current columns and other setup
     */
    public function __construct()
    {
        self::$columns = FlightImporter::$columns;
    }

    /**
     * Import a flight, parse out the different rows
     * @param Flight $flight
     * @return array
     */
    public function export(Flight $flight): array
    {
        $ret = [];
        foreach(self::$columns as $column) {
            $ret[$column] = $flight->{$column};
        }

        # Modify special fields
        $ret['airline'] = $ret['airline']->icao;
        $ret['distance'] = $ret['distance']->toNumber();

        $ret['fares'] = $this->getFares($flight);
        $ret['fields'] = $this->getFields($flight);
        $ret['subfleets'] = $this->getSubfleets($flight);

        return $ret;
    }

    /**
     * Return any custom fares that have been made to this flight
     * @param Flight $flight
     * @return string
     */
    protected function getFares(Flight &$flight): string
    {
        $fares = [];
        foreach($flight->fares as $fare) {
            $fare_export = [];
            if($fare->pivot->price) {
                $fare_export['price'] = $fare->pivot->price;
            }

            if ($fare->pivot->cost) {
                $fare_export['cost'] = $fare->pivot->cost;
            }

            if ($fare->pivot->capacity) {
                $fare_export['capacity'] = $fare->pivot->capacity;
            }

            $fares[$fare->code] = $fare_export;
        }

        return $this->objectToMultiString($fares);
    }

    /**
     * Parse all of the subfields
     * @param Flight $flight
     * @return string
     */
    protected function getFields(Flight &$flight): string
    {
        $ret = [];
        foreach ($flight->field_values as $field) {
            $ret[$field->name] = $field->value;
        }

        return $this->objectToMultiString($ret);
    }

    /**
     * Create the list of subfleets that are associated here
     * @param Flight $flight
     * @return string
     */
    protected function getSubfleets(Flight &$flight): string
    {
        $subfleets = [];
        foreach($flight->subfleets as $subfleet) {
            $subfleets[] = $subfleet->type;
        }

        return $this->objectToMultiString($subfleets);
    }
}
