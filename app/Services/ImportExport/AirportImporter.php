<?php

namespace App\Services\ImportExport;

use App\Contracts\ImportExport;
use App\Models\Airport;

/**
 * Import airports
 */
class AirportImporter extends ImportExport
{
    public $assetType = 'airport';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'icao'                 => 'required',
        'iata'                 => 'required',
        'name'                 => 'required',
        'location'             => 'nullable',
        'country'              => 'nullable',
        'timezone'             => 'nullable',
        'hub'                  => 'nullable|boolean',
        'lat'                  => 'required|numeric',
        'lon'                  => 'required|numeric',
        'ground_handling_cost' => 'nullable|numeric',
        'fuel_100ll_cost'      => 'nullable|numeric',
        'fuel_jeta_cost'       => 'nullable|numeric',
        'fuel_mogas_cost'      => 'nullable|numeric',
    ];

    /**
     * Import a flight, parse out the different rows
     *
     * @param array $row
     * @param int   $index
     *
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        $row['id'] = $row['icao'];
        $row['hub'] = get_truth_state($row['hub']);
        $row['ground_handling_cost'] = floatval($row['ground_handling_cost']);

        if ($row['ground_handling_cost'] === null && $row['ground_handling_cost'] !== 0) {
            $row['ground_handling_cost'] = setting('airports.default_ground_handling_cost');
        }

        if ($row['fuel_jeta_cost'] === null && $row['fuel_jeta_cost'] !== 0) {
            $row['fuel_jeta_cost'] = setting('airports.default_jet_a_fuel_cost');
        }

        try {
            Airport::updateOrCreate([
                'id' => $row['icao'],
            ], $row);
        } catch (\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->log('Imported '.$row['icao']);
        return true;
    }
}
