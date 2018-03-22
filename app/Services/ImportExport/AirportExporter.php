<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Airport;

/**
 * The flight importer can be imported or export. Operates on rows
 *
 * @package App\Services\Import
 */
class AirportExporter extends ImportExport
{
    public $assetType = 'airport';

    /**
     * Set the current columns and other setup
     */
    public function __construct()
    {
        self::$columns = AirportImporter::$columns;
    }

    /**
     * Import a flight, parse out the different rows
     * @param Airport $airport
     * @return array
     */
    public function export(Airport $airport): array
    {
        $ret = [];
        foreach(self::$columns as $column) {
            $ret[$column] = $airport->{$column};
        }

        return $ret;
    }
}
