<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Subfleet;

/**
 * Import subfleets
 * @package App\Services\Import
 */
class SubfleetImporter extends ImportExport
{
    public $assetType = 'subfleet';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline',
        'type',
        'name',
    ];

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index)
    {
        $airline = $this->getAirline($row['airline']);
        if(!$airline) {
            $this->status = 'Airline '.$row['airline'].' not found, row: '.$index;
            return false;
        }

        $row['airline_id'] = $airline->id;

        $subfleet = Subfleet::firstOrNew([
            'type'    => $row['type']
        ], $row);

        try {
            $subfleet->save();
        } catch(\Exception $e) {
            $this->status = 'Error in row '.$index.': '.$e->getMessage();
            return false;
        }

        $this->status = 'Imported ' . $row['type'];
        return true;
    }
}
