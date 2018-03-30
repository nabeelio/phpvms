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
        $this->importSvc = app(\App\Services\ImportService::class);
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

        $fare_business = factory(App\Models\Fare::class)->create(['code' => 'B', 'capacity' => 20]);
        $fare_svc->setForSubfleet($subfleet, $fare_business);

        # Add first class
        $fare_first = factory(App\Models\Fare::class)->create(['code' => 'F', 'capacity' => 10]);
        $fare_svc->setForSubfleet($subfleet, $fare_first);

        return [$airline, $subfleet];
    }

    /**
     * Test the parsing of different field/column which can be used
     * for specifying different field values
     */
    public function testConvertStringtoObjects(): void
    {
        $tests = [
            [
                'input' => '',
                'expected' => [],
            ],
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
                'input'    => 'Y?;F?price=1200',
                'expected' => [
                    'Y' => [],
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
            $this->assertEquals($test['expected'], $parsed);
        }
    }

    /**
     * Tests for converting the different object/array key values
     * into the format that we use in CSV files
     */
    public function testConvertObjectToString(): void
    {
        $tests = [
            [
                'input' => '',
                'expected' => ''
            ],
            [
                'input' => ['gate'],
                'expected'    => 'gate',
            ],
            [
                'input' => [
                    'gate',
                    'cost index',
                ],
                'expected' => 'gate;cost index',
            ],
            [
                'input' => [
                    'gate'       => 'B32',
                    'cost index' => '100'
                ],
                'expected' => 'gate=B32;cost index=100',
            ],
            [
                'input' => [
                    'Y' => [
                        'price' => 200,
                        'cost'  => 100,
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ],
                'expected' => 'Y?price=200&cost=100;F?price=1200',
            ],
            [
                'input' => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => [
                        'price' => 1200
                    ]
                ],
                'expected' => 'Y?price&cost;F?price=1200',
            ],
            [
                'input'    => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => []
                ],
                'expected' => 'Y?price&cost;F',
            ],
            [
                'input' => [
                    0   => 'Y',
                    'F' => [
                        'price' => 1200
                    ]
                ],
                'expected' => 'Y;F?price=1200',
            ],
            [
                'input' => [
                    'Departure Gate' => '4',
                    'Arrival Gate'   => 'C61',
                ],
                'expected' => 'Departure Gate=4;Arrival Gate=C61',
            ],
        ];

        foreach ($tests as $test) {
            $parsed = $this->importBaseClass->objectToMultiString($test['input']);
            $this->assertEquals($test['expected'], $parsed);
        }
    }

    /**
     * Test exporting all the flights to a file
     */
    public function testFlightExporter(): void
    {
        $fareSvc = app(FareService::class);

        [$airline, $subfleet] = $this->insertFlightsScaffoldData();
        $subfleet2 = factory(App\Models\Subfleet::class)->create(['type' => 'B74X']);

        $fareY = \App\Models\Fare::where('code', 'Y')->first();
        $fareF = \App\Models\Fare::where('code', 'F')->first();

        $flight = factory(App\Models\Flight::class)->create([
            'airline_id' => $airline->id,
            'flight_type' => 'J',
            'days' => \App\Models\Enums\Days::getDaysMask([
                \App\Models\Enums\Days::TUESDAY,
                \App\Models\Enums\Days::SUNDAY,
            ]),
        ]);

        $flight->subfleets()->syncWithoutDetaching([$subfleet->id, $subfleet2->id]);

        //
        $fareSvc->setForFlight($flight, $fareY, ['capacity' => '100']);
        $fareSvc->setForFlight($flight, $fareF);

        // Add some custom fields
        \App\Models\FlightFieldValue::create([
            'flight_id' => $flight->id,
            'name' => 'Departure Gate',
            'value' => '4'
        ]);

        \App\Models\FlightFieldValue::create([
            'flight_id' => $flight->id,
            'name'      => 'Arrival Gate',
            'value'     => 'C41'
        ]);

        // Test the conversion

        $exporter = new \App\Services\ImportExport\FlightExporter();
        $exported = $exporter->export($flight);

        $this->assertEquals('27', $exported['days']);
        $this->assertEquals('VMS', $exported['airline']);
        $this->assertEquals('J', $exported['flight_type']);
        $this->assertEquals('A32X;B74X', $exported['subfleets']);
        $this->assertEquals('Y?capacity=100;F', $exported['fares']);
        $this->assertEquals('Departure Gate=4;Arrival Gate=C41', $exported['fields']);
    }

    /**
     * Try importing the aicraft in the airports. Should fail
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function testInvalidFileImport(): void
    {
        $file_path = base_path('tests/data/aircraft.csv');
        $this->importSvc->importAirports($file_path);
    }

    /**
     * Try importing the aicraft in the airports. Should fail because of
     * empty/invalid rows
     */
    public function testEmptyCols(): void
    {
        $file_path = base_path('tests/data/expenses_empty_rows.csv');
        $status = $this->importSvc->importExpenses($file_path);
        $this->assertCount(8, $status['success']);
        $this->assertCount(0, $status['errors']);
    }

    /**
     * Test the importing of expenses
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testExpenseImporter(): void
    {
        $airline = factory(App\Models\Airline::class)->create(['icao' => 'VMS']);
        $subfleet = factory(App\Models\Subfleet::class)->create(['type' => '744-3X-RB211']);
        $aircraft = factory(App\Models\Aircraft::class)->create([
            'subfleet_id' => $subfleet->id,
            'registration' => '001Z',
        ]);

        $file_path = base_path('tests/data/expenses.csv');
        $status = $this->importSvc->importExpenses($file_path);

        $this->assertCount(8, $status['success']);
        $this->assertCount(0, $status['errors']);

        $expenses = \App\Models\Expense::all();

        $on_airline = $expenses->where('name', 'Per-Flight (multiplier, on airline)')->first();
        $this->assertEquals(200, $on_airline->amount);
        $this->assertEquals($airline->id, $on_airline->airline_id);

        $pf = $expenses->where('name', 'Per-Flight (no muliplier)')->first();
        $this->assertEquals(100, $pf->amount);
        $this->assertEquals(\App\Models\Enums\ExpenseType::FLIGHT, $pf->type);

        $catering = $expenses->where('name', 'Catering Staff')->first();
        $this->assertEquals(1000, $catering->amount);
        $this->assertEquals(\App\Models\Enums\ExpenseType::DAILY, $catering->type);
        $this->assertEquals(\App\Models\Subfleet::class, $catering->ref_class);
        $this->assertEquals($subfleet->id, $catering->ref_class_id);

        $mnt = $expenses->where('name', 'Maintenance')->first();
        $this->assertEquals(\App\Models\Aircraft::class, $mnt->ref_class);
        $this->assertEquals($aircraft->id, $mnt->ref_class_id);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testFareImporter(): void
    {
        $file_path = base_path('tests/data/fares.csv');
        $status = $this->importSvc->importFares($file_path);

        $this->assertCount(3, $status['success']);
        $this->assertCount(0, $status['errors']);

        $fares = \App\Models\Fare::all();

        $y_class = $fares->where('code', 'Y')->first();
        $this->assertEquals('Economy', $y_class->name);
        $this->assertEquals(100, $y_class->price);
        $this->assertEquals(0, $y_class->cost);
        $this->assertEquals(200, $y_class->capacity);
        $this->assertEquals(true, $y_class->active);
        $this->assertEquals('This is the economy class', $y_class->notes);

        $b_class = $fares->where('code', 'B')->first();
        $this->assertEquals('Business', $b_class->name);
        $this->assertEquals(500, $b_class->price);
        $this->assertEquals(250, $b_class->cost);
        $this->assertEquals(10, $b_class->capacity);
        $this->assertEquals('This is business class', $b_class->notes);
        $this->assertEquals(false, $b_class->active);

        $f_class = $fares->where('code', 'F')->first();
        $this->assertEquals('First-Class', $f_class->name);
        $this->assertEquals(800, $f_class->price);
        $this->assertEquals(350, $f_class->cost);
        $this->assertEquals(5, $f_class->capacity);
        $this->assertEquals('', $f_class->notes);
        $this->assertEquals(true, $f_class->active);
    }

    /**
     * Test the flight importer
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testFlightImporter(): void
    {
        [$airline, $subfleet] = $this->insertFlightsScaffoldData();

        $file_path = base_path('tests/data/flights.csv');
        $status = $this->importSvc->importFlights($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

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
        $this->assertEquals(FlightType::SCHED_PAX, $flight->flight_type);
        $this->assertEquals('ILEXY2 ZENZI LFK ELD J29 MEM Q29 JHW J70 STENT J70 MAGIO J70 LVZ LENDY6', $flight->route);
        $this->assertEquals('Just a flight', $flight->notes);
        $this->assertEquals(true, $flight->active);

        # Test that the days were set properly
        $this->assertTrue($flight->on_day(\App\Models\Enums\Days::MONDAY));
        $this->assertTrue($flight->on_day(\App\Models\Enums\Days::FRIDAY));
        $this->assertFalse($flight->on_day(\App\Models\Enums\Days::TUESDAY));

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
        $this->assertCount(3, $fares);

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
     * Test the flight importer
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testFlightImporterEmptyCustomFields(): void
    {
        [$airline, $subfleet] = $this->insertFlightsScaffoldData();

        $file_path = base_path('tests/data/flights_empty_fields.csv');
        $status = $this->importSvc->importFlights($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(0, $status['errors']);

        // See if it imported
        $flight = \App\Models\Flight::where([
            'airline_id'    => $airline->id,
            'flight_number' => '1972'
        ])->first();

        $this->assertNotNull($flight);

        // Check the custom fields entered
        $fields = \App\Models\FlightFieldValue::where([
            'flight_id' => $flight->id,
        ])->get();

        $this->assertCount(0, $fields);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testAircraftImporter(): void
    {
        $subfleet = factory(App\Models\Subfleet::class)->create(['type' => 'A32X']);

        $file_path = base_path('tests/data/aircraft.csv');
        $status = $this->importSvc->importAircraft($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testAirportImporter(): void
    {
        $file_path = base_path('tests/data/airports.csv');
        $status = $this->importSvc->importAirports($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testSubfleetImporter(): void
    {
        $fare_economy = factory(App\Models\Fare::class)->create(['code' => 'Y', 'capacity' => 150]);
        $fare_business = factory(App\Models\Fare::class)->create(['code' => 'B', 'capacity' => 20]);
        $airline = factory(App\Models\Airline::class)->create(['icao' => 'VMS']);

        $file_path = base_path('tests/data/subfleets.csv');
        $status = $this->importSvc->importSubfleets($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

        // See if it imported
        $subfleet = \App\Models\Subfleet::where([
            'type' => 'A32X',
        ])->first();

        $this->assertNotNull($subfleet);
        $this->assertEquals($airline->id, $subfleet->id);
        $this->assertEquals('A32X', $subfleet->type);
        $this->assertEquals('Airbus A320', $subfleet->name);

        // get the fares and check the pivot tables and the main tables
        $fares = $subfleet->fares()->get();

        $eco = $fares->where('code', 'Y')->first();
        $this->assertEquals(null, $eco->pivot->price);
        $this->assertEquals(null, $eco->pivot->capacity);
        $this->assertEquals(null, $eco->pivot->cost);

        $this->assertEquals($fare_economy->price, $eco->price);
        $this->assertEquals($fare_economy->capacity, $eco->capacity);
        $this->assertEquals($fare_economy->cost, $eco->cost);

        $busi = $fares->where('code', 'B')->first();
        $this->assertEquals($fare_business->price, $busi->price);
        $this->assertEquals($fare_business->capacity, $busi->capacity);
        $this->assertEquals($fare_business->cost, $busi->cost);

        $this->assertEquals('500%', $busi->pivot->price);
        $this->assertEquals(100, $busi->pivot->capacity);
        $this->assertEquals(null, $busi->pivot->cost);
    }
}
