<?php

namespace App\Services\Import;

use App\Interfaces\ImportExport;
use App\Models\Airport;

/**
 * Import airports
 * @package App\Services\Import
 */
class AirportImporter extends ImportExport
{
    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'iata',
        'icao',
        'name',
        'location',
        'country',
        'timezone',
        'hub',
        'lat',
        'lon',
    ];

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index)
    {
        $row['id'] = $row['icao'];
        $row['hub'] = get_truth_state($row['hub']);

        $airport = Airport::firstOrNew([
            'id'    => $row['icao']
        ], $row);

        try {
            $airport->save();
        } catch(\Exception $e) {
            $this->status = 'Error in row '.$index.': '.$e->getMessage();
            return false;
        }

        $this->status = 'Imported ' . $row['icao'];
        return true;
    }
}
