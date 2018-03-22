<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Fare;

/**
 * Import aircraft
 * @package App\Services\Import
 */
class FareImporter extends ImportExport
{
    public $assetType = 'fare';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'code',
        'name',
        'price',
        'cost',
        'capacity',
        'notes',
        'active',
    ];

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        # Try to add or update
        $fare = Fare::firstOrNew([
            'code' => $row['code'],
        ], $row);

        try {
            $fare->save();
        } catch(\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->log('Imported '.$row['code'].' '.$row['name']);
        return true;
    }
}
