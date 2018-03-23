<?php

namespace App\Services\ImportExport;

use App\Interfaces\ImportExport;
use App\Models\Airport;
use App\Models\Enums\FlightType;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use App\Services\FareService;
use App\Services\FlightService;
use Log;

/**
 * The flight importer can be imported or export. Operates on rows
 *
 * @package App\Services\Import
 */
class FlightImporter extends ImportExport
{
    public $assetType = 'flight';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline',
        'flight_number',
        'route_code',
        'route_leg',
        'dpt_airport',
        'arr_airport',
        'alt_airport',
        'days',
        'dpt_time',
        'arr_time',
        'level',
        'distance',
        'flight_time',
        'flight_type',
        'route',
        'notes',
        'active',
        'subfleets',
        'fares',
        'fields',
    ];

    /**
     *
     */
    private $fareSvc,
            $flightSvc;

    /**
     * FlightImportExporter constructor.
     */
    public function __construct()
    {
        $this->fareSvc = app(FareService::class);
        $this->flightSvc = app(FlightService::class);
    }

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index): bool
    {
        // Get the airline ID from the ICAO code
        $airline = $this->getAirline($row['airline']);

        // Try to find this flight
        $flight = Flight::firstOrNew([
            'airline_id'    => $airline->id,
            'flight_number' => $row['flight_number'],
            'route_code'    => $row['route_code'],
            'route_leg'     => $row['route_leg'],
        ], $row);

        // Airport atttributes
        $flight->setAttribute('dpt_airport_id', $row['dpt_airport']);
        $flight->setAttribute('arr_airport_id', $row['arr_airport']);
        if ($row['alt_airport']) {
            $flight->setAttribute('alt_airport_id', $row['alt_airport']);
        }

        // Any specific transformations
        // Flight type can be set to P - Passenger, C - Cargo, or H - Charter
        $flight->setAttribute('flight_type', FlightType::getFromCode($row['flight_type']));
        $flight->setAttribute('active', get_truth_state($row['active']));

        try {
            $flight->save();
        } catch (\Exception $e) {
            $this->errorLog('Error in row '.$index.': '.$e->getMessage());
            return false;
        }

        // Create/check that they exist
        $this->processAirport($row['dpt_airport']);
        $this->processAirport($row['arr_airport']);
        if ($row['alt_airport']) {
            $this->processAirport($row['alt_airport']);
        }

        $this->processSubfleets($flight, $row['subfleets']);
        $this->processFares($flight, $row['fares']);
        $this->processFields($flight, $row['fields']);

        $this->log('Imported row '.$index);
        return true;
    }

    /**
     * Process the airport
     * @param $airport
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function processAirport($airport)
    {
        return Airport::firstOrCreate([
            'icao' => $airport,
        ], ['name' => $airport]);
    }

    /**
     * Parse out all of the subfleets and associate them to the flight
     * The subfleet is created if it doesn't exist
     * @param Flight $flight
     * @param        $col
     */
    protected function processSubfleets(Flight &$flight, $col): void
    {
        $count = 0;
        $subfleets = $this->parseMultiColumnValues($col);
        foreach($subfleets as $subfleet_type) {
            $subfleet = Subfleet::firstOrCreate(
                ['type' => $subfleet_type],
                ['name' => $subfleet_type]
            );

            $subfleet->save();

            # sync
            $flight->subfleets()->syncWithoutDetaching([$subfleet->id]);
            $count ++;
        }

        Log::info('Subfleets added/processed: '.$count);
    }

    /**
     * Parse all of the fares in the multi-format
     * @param Flight $flight
     * @param        $col
     */
    protected function processFares(Flight &$flight, $col): void
    {
        $fares = $this->parseMultiColumnValues($col);
        foreach ($fares as $fare_code => $fare_attributes) {
            if (\is_int($fare_code)) {
                $fare_code = $fare_attributes;
                $fare_attributes = [];
            }

            $fare = Fare::firstOrCreate(['code' => $fare_code], ['name' => $fare_code]);
            $this->fareSvc->setForFlight($flight, $fare, $fare_attributes);
        }
    }

    /**
     * Parse all of the subfields
     * @param Flight $flight
     * @param        $col
     */
    protected function processFields(Flight &$flight, $col): void
    {
        $pass_fields = [];
        $fields = $this->parseMultiColumnValues($col);
        foreach($fields as $field_name => $field_value) {
            $pass_fields[] = [
                'name' => $field_name,
                'value' => $field_value,
            ];
        }

        $this->flightSvc->updateCustomFields($flight, $pass_fields);
    }
}
