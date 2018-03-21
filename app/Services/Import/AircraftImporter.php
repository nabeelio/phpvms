<?php

namespace App\Services\Import;

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
    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'subfleet',
        'name',
        'registration',
        'hex_code',
        'status',
    ];

    /**
     * Find the subfleet specified, or just create it on the fly
     * @param $type
     * @return Subfleet|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    protected function getSubfleet($type)
    {
        $subfleet = Subfleet::where(['type' => $type])->first();
        if (!$subfleet) {
            $subfleet = new Subfleet([
                'type' => $type,
                'name' => $type,
            ]);

            $subfleet->save();
        }

        return $subfleet;
    }

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index)
    {
        $subfleet = $this->getSubfleet($row['subfleet']);

        $row['subfleet_id'] = $subfleet->id;

        # Generate a hex code
        if(!$row['hex_code']) {
            $row['hex_code'] = ICAO::createHexCode();
        }

        # Set a default status
        if($row['status'] === null) {
            $row['status'] = AircraftStatus::ACTIVE;
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
            $this->status = 'Error in row '.$index.': '.$e->getMessage();
            return false;
        }

        $this->status = 'Imported '.$row['registration'].' '.$row['name'];
        return true;
    }
}
