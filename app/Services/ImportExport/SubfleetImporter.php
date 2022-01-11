<?php

namespace App\Services\ImportExport;

use App\Contracts\ImportExport;
use App\Models\Fare;
use App\Models\Subfleet;
use App\Services\FareService;

/**
 * Import subfleets
 */
class SubfleetImporter extends ImportExport
{
    public $assetType = 'subfleet';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline'                    => 'required',
        'hub_id'                     => 'nullable',
        'type'                       => 'required',
        'simbrief_type'              => 'nullable',
        'name'                       => 'required',
        'fuel_type'                  => 'nullable',
        'cost_block_hour'            => 'nullable',
        'cost_delay_minute'          => 'nullable',
        'ground_handling_multiplier' => 'nullable',
        'fares'                      => 'nullable',
    ];

    private $fareSvc;

    /**
     * FlightImportExporter constructor.
     */
    public function __construct()
    {
        $this->fareSvc = app(FareService::class);
    }

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
        $airline = $this->getAirline($row['airline']);
        $row['airline_id'] = $airline->id;

        try {
            $subfleet = Subfleet::updateOrCreate([
                'type' => $row['type'],
            ], $row);
        } catch (\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        $this->processFares($subfleet, $row['fares']);

        $this->log('Imported '.$row['type']);
        return true;
    }

    /**
     * Parse all of the fares in the multi-format
     *
     * @param Subfleet $subfleet
     * @param          $col
     */
    protected function processFares(Subfleet &$subfleet, $col): void
    {
        $fares = $this->parseMultiColumnValues($col);
        foreach ($fares as $fare_code => $fare_attributes) {
            if (\is_int($fare_code)) {
                $fare_code = $fare_attributes;
                $fare_attributes = [];
            }

            $fare = Fare::firstOrCreate(['code' => $fare_code], ['name' => $fare_code]);
            $this->fareSvc->setForSubfleet($subfleet, $fare, $fare_attributes);
            $fare->save();
        }
    }
}
