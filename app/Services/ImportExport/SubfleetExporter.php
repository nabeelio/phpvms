<?php

namespace App\Services\ImportExport;

use App\Contracts\ImportExport;
use App\Models\Flight;
use App\Models\Subfleet;

/**
 * The flight importer can be imported or export. Operates on rows
 */
class SubfleetExporter extends ImportExport
{
    public $assetType = 'subfleet';

    /**
     * Set the current columns and other setup
     */
    public function __construct()
    {
        self::$columns = array_keys(SubfleetImporter::$columns);
    }

    /**
     * Import a flight, parse out the different rows
     *
     * @param Subfleet $subfleet
     *
     * @return array
     */
    public function export($subfleet): array
    {
        $ret = [];
        foreach (self::$columns as $column) {
            $ret[$column] = $subfleet->{$column};
        }

        // Modify special fields
        $ret['airline'] = $subfleet->airline->icao;
        $ret['fares'] = $this->getFares($subfleet);

        return $ret;
    }

    /**
     * Return any custom fares that have been made to this flight
     *
     * @param Subfleet $subfleet
     *
     * @return string
     */
    protected function getFares(Subfleet &$subfleet): string
    {
        $fares = [];
        foreach ($subfleet->fares as $fare) {
            $fare_export = [];
            if ($fare->pivot->price) {
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
     *
     * @param Flight $flight
     *
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
     *
     * @param Flight $flight
     *
     * @return string
     */
    protected function getSubfleets(Flight &$flight): string
    {
        $subfleets = [];
        foreach ($flight->subfleets as $subfleet) {
            $subfleets[] = $subfleet->type;
        }

        return $this->objectToMultiString($subfleets);
    }
}
