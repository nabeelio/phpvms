<?php

namespace App\Services\ImportExport;

use App\Contracts\ImportExport;
use App\Models\Airport;
use App\Models\Enums\Days;
use App\Models\Enums\FlightType;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use App\Services\AirportService;
use App\Services\FareService;
use App\Services\FlightService;
use Illuminate\Support\Facades\Log;

/**
 * The flight importer can be imported or export. Operates on rows
 */
class FlightImporter extends ImportExport
{
    public $assetType = 'flight';

    /**
     * All of the columns that are in the CSV import
     * Should match the database fields, for the most part
     */
    public static $columns = [
        'airline'              => 'required',
        'flight_number'        => 'required',
        'route_code'           => 'nullable',
        'callsign'             => 'nullable',
        'route_leg'            => 'nullable',
        'dpt_airport'          => 'required',
        'arr_airport'          => 'required',
        'alt_airport'          => 'nullable',
        'days'                 => 'nullable',
        'dpt_time'             => 'nullable',
        'arr_time'             => 'nullable',
        'level'                => 'nullable|integer',
        'distance'             => 'nullable|numeric',
        'flight_time'          => 'required|integer',
        'flight_type'          => 'required|alpha',
        'load_factor'          => 'nullable',
        'load_factor_variance' => 'nullable',
        'pilot_pay'            => 'nullable',
        'route'                => 'nullable',
        'notes'                => 'nullable',
        'start_date'           => 'nullable|date',
        'end_date'             => 'nullable|date',
        'active'               => 'nullable|boolean',
        'subfleets'            => 'nullable',
        'fares'                => 'nullable',
        'fields'               => 'nullable',
    ];

    private $airportSvc;
    private $fareSvc;
    private $flightSvc;

    /**
     * FlightImportExporter constructor.
     */
    public function __construct()
    {
        $this->airportSvc = app(AirportService::class);
        $this->fareSvc = app(FareService::class);
        $this->flightSvc = app(FlightService::class);
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
        // Get the airline ID from the ICAO code
        $airline = $this->getAirline($row['airline']);

        // Try to find this flight
        /** @var Flight $flight */
        $flight = Flight::firstOrNew([
            'airline_id'    => $airline->id,
            'flight_number' => $row['flight_number'],
            'route_code'    => $row['route_code'],
            'route_leg'     => $row['route_leg'],
        ], $row);

        $row['dpt_airport'] = strtoupper($row['dpt_airport']);
        $row['arr_airport'] = strtoupper($row['arr_airport']);

        // Airport atttributes
        $flight->setAttribute('days', $this->setDays($row['days']));
        $flight->setAttribute('dpt_airport_id', $row['dpt_airport']);
        $flight->setAttribute('arr_airport_id', $row['arr_airport']);
        if ($row['alt_airport']) {
            $flight->setAttribute('alt_airport_id', $row['alt_airport']);
        }

        // Handle Route and Level Fields
        $flight->setAttribute('route', strtoupper($row['route']));
        $flight->setAttribute('level', $row['level']);

        // Any specific transformations

        // Check for a valid value
        $flight_type = $row['flight_type'];
        if (!array_key_exists($flight_type, FlightType::labels())) {
            $flight_type = FlightType::SCHED_PAX;
        }

        $flight->setAttribute('flight_type', $flight_type);
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

        // Check/calculate the distance
        if (empty($row['distance'])) {
            $row['distance'] = $this->airportSvc->calculateDistance(
                $row['dpt_airport'],
                $row['arr_airport']
            );
        }

        $this->processSubfleets($flight, $row['subfleets']);
        $this->processFares($flight, $row['fares']);
        $this->processFields($flight, $row['fields']);

        $this->log('Imported row '.$index);
        return true;
    }

    /**
     * Return the mask of the days
     *
     * @param $day_str
     *
     * @return int|mixed
     */
    protected function setDays($day_str)
    {
        if (!$day_str) {
            return 0;
        }

        $days = [];
        if (strpos($day_str, '1') !== false) {
            $days[] = Days::MONDAY;
        }

        if (strpos($day_str, '2') !== false) {
            $days[] = Days::TUESDAY;
        }

        if (strpos($day_str, '3') !== false) {
            $days[] = Days::WEDNESDAY;
        }

        if (strpos($day_str, '4') !== false) {
            $days[] = Days::THURSDAY;
        }

        if (strpos($day_str, '5') !== false) {
            $days[] = Days::FRIDAY;
        }

        if (strpos($day_str, '6') !== false) {
            $days[] = Days::SATURDAY;
        }

        if (strpos($day_str, '7') !== false) {
            $days[] = Days::SUNDAY;
        }

        return Days::getDaysMask($days);
    }

    /**
     * Process the airport
     *
     * @param $airport
     *
     * @return Airport
     */
    protected function processAirport($airport): Airport
    {
        return $this->airportSvc->lookupAirportIfNotFound($airport);
    }

    /**
     * Parse out all of the subfleets and associate them to the flight
     * The subfleet is created if it doesn't exist
     *
     * @param Flight $flight
     * @param        $col
     */
    protected function processSubfleets(Flight &$flight, $col): void
    {
        $count = 0;
        $subfleets = $this->parseMultiColumnValues($col);
        foreach ($subfleets as $subfleet_type) {
            $subfleet_type = trim($subfleet_type);
            if (empty($subfleet_type)) {
                continue;
            }

            $subfleet = Subfleet::firstOrCreate(
                ['type' => $subfleet_type],
                [
                    'name'       => $subfleet_type,
                    'airline_id' => $flight->airline_id,
                ]
            );

            $subfleet->save();

            // sync
            $flight->subfleets()->syncWithoutDetaching([$subfleet->id]);
            $count++;
        }

        Log::info('Subfleets added/processed: '.$count);
    }

    /**
     * Parse all of the fares in the multi-format
     *
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
            $fare->save();
        }
    }

    /**
     * Parse all of the subfields
     *
     * @param Flight $flight
     * @param        $col
     */
    protected function processFields(Flight &$flight, $col): void
    {
        $pass_fields = [];
        $fields = $this->parseMultiColumnValues($col);
        foreach ($fields as $field_name => $field_value) {
            $pass_fields[] = [
                'name'  => $field_name,
                'value' => $field_value,
            ];
        }

        $this->flightSvc->updateCustomFields($flight, $pass_fields);
    }
}
