<?php

namespace App\Services\Import;

use App\Interfaces\ImportExport;
use App\Models\Enums\FlightType;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use App\Repositories\AirlineRepository;
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
    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline',
        'flight_number',
        'route_code',
        'route_leg',
        'dpt_airport_id',
        'arr_airport_id',
        'alt_airport_id',
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
    private $airlineRepo,
            $fareSvc,
            $flightSvc;

    /**
     * FlightImportExporter constructor.
     */
    public function __construct()
    {
        $this->airlineRepo = app(AirlineRepository::class);
        $this->fareSvc = app(FareService::class);
        $this->flightSvc = app(FlightService::class);
    }

    /**
     * Import a flight, parse out the different rows
     * @param array $row
     * @param int   $index
     * @return bool
     */
    public function import(array $row, $index)
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

        // Any specific transformations
        // Flight type can be set to P - Passenger, C - Cargo, or H - Charter
        $flight->setAttribute('flight_type', FlightType::getFromCode($row['flight_type']));
        $flight->setAttribute('active', get_truth_state($row['active']));

        try {
            $flight->save();
        } catch (\Exception $e) {
            $this->status = 'Error in row '.$index.': '.$e->getMessage();
            return false;
        }

        $this->processSubfleets($flight, $row['subfleets']);
        $this->processFares($flight, $row['fares']);
        $this->processFields($flight, $row['fields']);

        $this->status = 'Imported row '.$index;
        return true;
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
            $subfleet = Subfleet::firstOrNew(
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

            $fare = Fare::firstOrNew(['code' => $fare_code], ['name' => $fare_code]);
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
