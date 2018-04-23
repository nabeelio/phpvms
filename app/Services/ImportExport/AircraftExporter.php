<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Aircraft;
use App\Models\Enums\AircraftStatus;
use App\Models\Flight;

/**
 * The flight importer can be imported or export. Operates on rows
 *
 * @package App\Services\Import
 */
class AircraftExporter extends ImportExport
{
    public $assetType = 'aircraft';

    /**
     * Set the current columns and other setup
     */
    public function __construct()
    {
        self::$columns = array_keys(AircraftImporter::$columns);
    }

    /**
     * Import a flight, parse out the different rows
     * @param Aircraft $aircraft
     * @return array
     */
    public function export($aircraft): array
    {
        $ret = [];
        foreach(self::$columns as $column) {
            $ret[$column] = $aircraft->{$column};
        }

        # Modify special fields
        $ret['subfleet'] = $aircraft->subfleet->type;

        return $ret;
    }
}
