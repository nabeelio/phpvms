<?php

use App\Services\FareService;
use App\Models\Enums\FlightType;

/**
 * Class ImporterTest
 */
class ImporterTest extends TestCase
{
    private $importBaseClass,
            $importSvc,
            $fareSvc;

    public function setUp()
    {
        parent::setUp();
        $this->importBaseClass = new \App\Interfaces\ImportExport();
        $this->importSvc = app(\App\Services\ImporterService::class);
        $this->fareSvc = app(\App\Services\FareService::class);
    }

    /**
     * Add some of the basic data needed to properly import the flights.csv file
     * @return mixed
     */
    protected function insertFlightsScaffoldData()
    {
        $fare_svc = app(FareService::class);

        $al = [
            'icao' => 'VMS',
            'name' => 'phpVMS Airlines',
        ];

        $airline = factory(App\Models\Airline::class)->create($al);
        $subfleet = factory(App\Models\Subfleet::class)->create(['type' => 'A32X']);

        # Add the economy class
        $fare_economy = factory(App\Models\Fare::class)->create(['code' => 'Y', 'capacity' => 150]);
        $fare_svc->setForSubfleet($subfleet, $fare_economy);

        # Add first class
        $fare_first = factory(App\Models\Fare::class)->create(['code' => 'F', 'capacity' => 10]);
        $fare_svc->setForSubfleet($subfleet, $fare_first);

        return $airline;
    }

    /**
     * Test the parsing of different field/column which can be used
     * for specifying different field values
     */
    public function testMultiFieldValues()
    {
        $tests = [
            [
                'input' => 'gate',
                'expected' => ['gate']
            ],
            [
                'input' => 'gate;cost index',
                'expected' => [
                    'gate',
                    'cost index',
                ]
            ],
            [
                'input' => 'gate=B32;cost index=100',
                'expected' => [
                    'gate' => 'B32',
                    'cost index' => '100'
                ]
            ],
            [
                'input' => 'Y?price=200&cost=100; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price' => 200,
                        'cost' => 100,
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ]
            ],
            [
                'input' => 'Y?price&cost; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ]
            ],
            [
                'input'    => 'Y; F?price=1200',
                'expected' => [
                    0 => 'Y',
                    'F' => [
                        'price' => 1200
                    ]
                ]
            ],
            [
                'input' => 'Departure Gate=4;Arrival Gate=C61',
                'expected' => [
                    'Departure Gate' => '4',
                    'Arrival Gate' => 'C61',
                ]
            ],
        ];

        foreach($tests as $test) {
            $parsed = $this->importBaseClass->parseMultiColumnValues($test['input']);
            $this->assertEquals($parsed, $test['expected']);
        }
    }

    /**
     * Test the flight importer
     * @throws \League\Csv\Exception
     */
    public function testFlightImporter(): void
    {
        $airline = $this->insertFlightsScaffoldData();

        $file_path = base_path('tests/data/flights.csv');
        $this->importSvc->importFlights($file_path);

        // See if it imported
        $flight = \App\Models\Flight::where([
            'airline_id'    => $airline->id,
            'flight_number' => '1972'
        ])->first();

        $this->assertNotNull($flight);

        // Check the flight itself
        $this->assertEquals('KAUS', $flight->dpt_airport_id);
        $this->assertEquals('KJFK', $flight->arr_airport_id);
        $this->assertEquals('0810 CST', $flight->dpt_time);
        $this->assertEquals('1235 EST', $flight->arr_time);
        $this->assertEquals('350', $flight->level);
        $this->assertEquals('1477', $flight->distance);
        $this->assertEquals('207', $flight->flight_time);
        $this->assertEquals(FlightType::PASSENGER, $flight->flight_type);
        $this->assertEquals('ILEXY2 ZENZI LFK ELD J29 MEM Q29 JHW J70 STENT J70 MAGIO J70 LVZ LENDY6', $flight->route);
        $this->assertEquals('Just a flight', $flight->notes);
        $this->assertEquals(true, $flight->active);

        // Check the custom fields entered
        $fields = \App\Models\FlightFieldValue::where([
            'flight_id' => $flight->id,
        ])->get();

        $this->assertCount(2, $fields);
        $dep_gate = $fields->where('name', 'Departure Gate')->first();
        $this->assertEquals('4', $dep_gate['value']);

        $dep_gate = $fields->where('name', 'Arrival Gate')->first();
        $this->assertEquals('C41', $dep_gate['value']);

        // Check the fare class
        $fares = $this->fareSvc->getForFlight($flight);
        $this->assertCount(2, $fares);

        $first = $fares->where('code', 'Y')->first();
        $this->assertEquals(300, $first->price);
        $this->assertEquals(100, $first->cost);
        $this->assertEquals(130, $first->capacity);

        $first = $fares->where('code', 'F')->first();
        $this->assertEquals(600, $first->price);
        $this->assertEquals(400, $first->cost);
        $this->assertEquals(10, $first->capacity);

        // Check the subfleets
        $subfleets = $flight->subfleets;
        $this->assertCount(1, $subfleets);
    }

    /**
     *
     * @throws \League\Csv\Exception
     */
    public function testAircraftImporter()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create(['type' => 'A32X']);

        $file_path = base_path('tests/data/aircraft.csv');
        $this->importSvc->importAircraft($file_path);

        // See if it imported
        $aircraft = \App\Models\Aircraft::where([
            'registration' => 'N309US',
        ])->first();

        $this->assertNotNull($aircraft);
        $this->assertEquals($subfleet->id, $aircraft->id);
        $this->assertEquals('A320-211', $aircraft->name);
        $this->assertEquals('N309US', $aircraft->registration);
    }

    /**
     *
     * @throws \League\Csv\Exception
     */
    public function testAirportImporter()
    {
        $file_path = base_path('tests/data/airports.csv');
        $this->importSvc->importAirports($file_path);

        // See if it imported
        $airport = \App\Models\Airport::where([
            'id' => 'KAUS',
        ])->first();

        $this->assertNotNull($airport);
        $this->assertEquals('KAUS', $airport->id);
        $this->assertEquals('AUS', $airport->iata);
        $this->assertEquals('KAUS', $airport->icao);
    }

    /**
     * Test importing the subfleets
     * @throws \League\Csv\Exception
     */
    public function testSubfleetImporter(): void
    {
        $airline = factory(App\Models\Airline::class)->create(['icao' => 'VMS']);

        $file_path = base_path('tests/data/subfleets.csv');
        $this->importSvc->importSubfleets($file_path);

        // See if it imported
        $subfleet = \App\Models\Subfleet::where([
            'type' => 'A32X',
        ])->first();

        $this->assertNotNull($subfleet);
        $this->assertEquals($airline->id, $subfleet->id);
        $this->assertEquals('A32X', $subfleet->type);
        $this->assertEquals('Airbus A320', $subfleet->name);
    }
}
