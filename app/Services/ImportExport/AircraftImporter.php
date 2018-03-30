<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Aircraft;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Models\Subfleet;
use App\Support\ICAO;

/**
 * Import aircraft
 * @package App\Services\Import
 */
class AircraftImporter extends ImportExport
{
    public $assetType = 'aircraft';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'subfleet'     => 'required',
        'name'         => 'required',
        'registration' => 'required',
        'hex_code'     => 'nullable',
        'status'       => 'nullable',
    ];

    /**
     * Find the subfleet specified, or just create it on the fly
     * @param $type
     * @return Subfleet|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    protected function getSubfleet($type)
    {
        $subfleet = Subfleet::firstOrCreate([
            'type' => $type,
        ], ['name' => $type]);

        return $subfleet;
    }

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        $subfleet = $this->getSubfleet($row['subfleet']);
        $row['subfleet_id'] = $subfleet->id;

        # Generate a hex code
        if(!$row['hex_code']) {
            $row['hex_code'] = ICAO::createHexCode();
        }

        # Set a default status
        $row['status'] = trim($row['status']);
        if($row['status'] === null || $row['status'] === '') {
            $row['status'] = AircraftStatus::ACTIVE;
        } else {
            $row['status'] = AircraftStatus::getFromCode($row['status']);
        }

        # Just set its state right now as parked
        $row['state'] = AircraftState::PARKED;

        # Try to add or update
        $aircraft = Aircraft::firstOrNew([
            'registration' => $row['registration'],
        ], $row);

        try {
            $aircraft->save();
        } catch(\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->log('Imported '.$row['registration'].' '.$row['name']);
        return true;
    }
}
