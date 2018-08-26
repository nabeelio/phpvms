<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Fare;

/**
 * The flight importer can be imported or export. Operates on rows
 */
class FareExporter extends ImportExport
{
    public $assetType = 'fare';

    /**
     * Set the current columns and other setup
     */
    public function __construct()
    {
        self::$columns = array_keys(FareImporter::$columns);
    }

    /**
     * Import a flight, parse out the different rows
     *
     * @param Fare $fare
     *
     * @return array
     */
    public function export($fare): array
    {
        $ret = [];
        foreach (self::$columns as $column) {
            $ret[$column] = $fare->{$column};
        }

        return $ret;
    }
}
