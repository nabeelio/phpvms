<?php

namespace Tests;

use App\Contracts\ImportExport;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\AircraftStatus;
use App\Models\Enums\Days;
use App\Models\Enums\ExpenseType;
use App\Models\Enums\FlightType;
use App\Models\Expense;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\FlightFieldValue;
use App\Models\Subfleet;
use App\Services\ExportService;
use App\Services\FareService;
use App\Services\ImportExport\AircraftExporter;
use App\Services\ImportExport\AirportExporter;
use App\Services\ImportExport\FlightExporter;
use App\Services\ImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImporterTest extends TestCase
{
    private $importBaseClass;
    private $importSvc;
    private $fareSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->importBaseClass = new ImportExport();
        $this->importSvc = app(ImportService::class);
        $this->fareSvc = app(FareService::class);

        Storage::fake('local');
    }

    /**
     * Add some of the basic data needed to properly import the flights.csv file
     *
     * @return mixed
     */
    protected function insertFlightsScaffoldData()
    {
        $fare_svc = app(FareService::class);

        $al = [
            'icao' => 'VMS',
            'name' => 'phpVMS Airlines',
        ];

        $airline = Airline::factory()->create($al);
        $subfleet = Subfleet::factory()->create(['type' => 'A32X']);

        // Add the economy class
        $fare_economy = Fare::factory()->create(['code' => 'Y', 'capacity' => 150]);
        $fare_svc->setForSubfleet($subfleet, $fare_economy);

        $fare_business = Fare::factory()->create(['code' => 'B', 'capacity' => 20]);
        $fare_svc->setForSubfleet($subfleet, $fare_business);

        // Add first class
        $fare_first = Fare::factory()->create(['code' => 'F', 'capacity' => 10]);
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
                'input'    => '',
                'expected' => [],
            ],
            [
                'input'    => 'gate',
                'expected' => ['gate'],
            ],
            [
                'input'    => 'gate;cost index',
                'expected' => [
                    'gate',
                    'cost index',
                ],
            ],
            [
                'input'    => 'gate=B32;cost index=100',
                'expected' => [
                    'gate'       => 'B32',
                    'cost index' => '100',
                ],
            ],
            [
                'input'    => 'Y?price=200&cost=100; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price' => 200,
                        'cost'  => 100,
                    ],
                    'F' => [
                        'price' => 1200,
                    ],
                ],
            ],
            [
                'input'    => 'Y?price&cost; F?price=1200',
                'expected' => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => [
                        'price' => 1200,
                    ],
                ],
            ],
            [
                'input'    => 'Y; F?price=1200',
                'expected' => [
                    0   => 'Y',
                    'F' => [
                        'price' => 1200,
                    ],
                ],
            ],
            [
                'input'    => 'Y?;F?price=1200',
                'expected' => [
                    'Y' => [],
                    'F' => [
                        'price' => 1200,
                    ],
                ],
            ],
            [
                'input'    => 'Departure Gate=4;Arrival Gate=C61',
                'expected' => [
                    'Departure Gate' => '4',
                    'Arrival Gate'   => 'C61',
                ],
            ],
            // Blank values omitted
            [
                'input'    => 'gate; ',
                'expected' => [
                    'gate',
                ],
            ],
        ];

        foreach ($tests as $test) {
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
                'input'    => '',
                'expected' => '',
            ],
            [
                'input'    => ['gate'],
                'expected' => 'gate',
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
                    'cost index' => '100',
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
                        'price' => 1200,
                    ],
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
                        'price' => 1200,
                    ],
                ],
                'expected' => 'Y?price&cost;F?price=1200',
            ],
            [
                'input' => [
                    'Y' => [
                        'price',
                        'cost',
                    ],
                    'F' => [],
                ],
                'expected' => 'Y?price&cost;F',
            ],
            [
                'input' => [
                    0   => 'Y',
                    'F' => [
                        'price' => 1200,
                    ],
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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testAircraftExporter(): void
    {
        $aircraft = Aircraft::factory()->create();

        $exporter = new AircraftExporter();
        $exported = $exporter->export($aircraft);

        $this->assertEquals($aircraft->iata, $exported['iata']);
        $this->assertEquals($aircraft->icao, $exported['icao']);
        $this->assertEquals($aircraft->name, $exported['name']);
        $this->assertEquals($aircraft->zfw, $exported['zfw']);
        $this->assertEquals($aircraft->subfleet->type, $exported['subfleet']);

        $importer = app(ImportService::class);
        $exporter = app(ExportService::class);

        $collection = collect([$aircraft]);
        $file = $exporter->exportAircraft($collection);

        $status = $importer->importAircraft($file);
        $this->assertCount(1, $status['success']);
        $this->assertCount(0, $status['errors']);
    }

    /**
     * Test exporting all the flights to a file
     */
    public function testAirportExporter(): void
    {
        $airport_name = 'Adolfo Suárez Madrid–Barajas Airport';

        $airport = Airport::factory()->create([
            'name' => $airport_name,
        ]);

        $exporter = new AirportExporter();
        $exported = $exporter->export($airport);

        $this->assertEquals($airport->iata, $exported['iata']);
        $this->assertEquals($airport->icao, $exported['icao']);
        $this->assertEquals($airport->name, $exported['name']);

        $importer = app(ImportService::class);
        $exporter = app(ExportService::class);
        $file = $exporter->exportAirports(collect([$airport]));
        $status = $importer->importAirports($file);

        $this->assertCount(1, $status['success']);
        $this->assertCount(0, $status['errors']);
    }

    /**
     * Test exporting all the flights to a file
     */
    public function testFlightExporter(): void
    {
        $fareSvc = app(FareService::class);

        [$airline, $subfleet] = $this->insertFlightsScaffoldData();
        $subfleet2 = Subfleet::factory()->create(['type' => 'B74X']);

        $fareY = Fare::where('code', 'Y')->first();
        $fareF = Fare::where('code', 'F')->first();

        $flight = Flight::factory()->create([
            'airline_id'  => $airline->id,
            'flight_type' => 'J',
            'days'        => Days::getDaysMask([
                Days::TUESDAY,
                Days::SUNDAY,
            ]),
        ]);

        $flight->subfleets()->syncWithoutDetaching([$subfleet->id, $subfleet2->id]);

        //
        $fareSvc->setForFlight($flight, $fareY, ['capacity' => '100']);
        $fareSvc->setForFlight($flight, $fareF);

        // Add some custom fields
        FlightFieldValue::create([
            'flight_id' => $flight->id,
            'name'      => 'Departure Gate',
            'value'     => '4',
        ]);

        FlightFieldValue::create([
            'flight_id' => $flight->id,
            'name'      => 'Arrival Gate',
            'value'     => 'C41',
        ]);

        // Test the conversion

        $exporter = new FlightExporter();
        $exported = $exporter->export($flight);

        $this->assertEquals('27', $exported['days']);
        $this->assertEquals('VMS', $exported['airline']);
        $this->assertEquals($flight->flight_time, $exported['flight_time']);
        $this->assertEquals('J', $exported['flight_type']);
        $this->assertEquals('A32X;B74X', $exported['subfleets']);
        $this->assertEquals('Y?capacity=100;F', $exported['fares']);
        $this->assertEquals('Departure Gate=4;Arrival Gate=C41', $exported['fields']);

        $importer = app(ImportService::class);
        $exporter = app(ExportService::class);
        $file = $exporter->exportFlights(collect([$flight]));
        $status = $importer->importFlights($file);
        $this->assertCount(1, $status['success']);
        $this->assertCount(0, $status['errors']);
    }

    /**
     * Try importing the aicraft in the airports. Should fail
     */
    public function testInvalidFileImport(): void
    {
        $this->expectException(ValidationException::class);
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
     * @throws \League\Csv\CannotInsertRecord
     *
     * @return void
     */
    public function testExpenseExporter(): void
    {
        $expenses = Expense::factory(10)->create();

        /** @var ExportService $exporter */
        $exporter = app(ExportService::class);
        $exporter->exportExpenses($expenses);
    }

    /**
     * Test the importing of expenses
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testExpenseImporter(): void
    {
        $airline = Airline::factory()->create(['icao' => 'VMS']);
        $subfleet = Subfleet::factory()->create(['type' => '744-3X-RB211']);
        $aircraft = Aircraft::factory()->create([
            'subfleet_id'  => $subfleet->id,
            'registration' => '001Z',
        ]);

        $file_path = base_path('tests/data/expenses.csv');
        $status = $this->importSvc->importExpenses($file_path);

        $this->assertCount(8, $status['success']);
        $this->assertCount(0, $status['errors']);

        $expenses = Expense::all();

        $on_airline = $expenses->where('name', 'Per-Flight (multiplier, on airline)')->first();
        $this->assertEquals(200, $on_airline->amount);
        $this->assertEquals($airline->id, $on_airline->airline_id);

        $pf = $expenses->where('name', 'Per-Flight (no muliplier)')->first();
        $this->assertEquals(100, $pf->amount);
        $this->assertEquals(ExpenseType::FLIGHT, $pf->type);

        $catering = $expenses->where('name', 'Catering Staff')->first();
        $this->assertEquals(1000, $catering->amount);
        $this->assertEquals(ExpenseType::DAILY, $catering->type);
        $this->assertEquals(Subfleet::class, $catering->ref_model);
        $this->assertEquals($subfleet->id, $catering->ref_model_id);

        $mnt = $expenses->where('name', 'Maintenance')->first();
        $this->assertEquals(Aircraft::class, $mnt->ref_model);
        $this->assertEquals($aircraft->id, $mnt->ref_model_id);
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

        $fares = Fare::all();

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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testFlightImporter(): void
    {
        [$airline, $subfleet] = $this->insertFlightsScaffoldData();

        $file_path = base_path('tests/data/flights.csv');
        $status = $this->importSvc->importFlights($file_path);

        $this->assertCount(3, $status['success']);
        $this->assertCount(1, $status['errors']);

        // See if it imported
        /** @var Flight $flight */
        $flight = Flight::where([
            'airline_id'    => $airline->id,
            'flight_number' => '1972',
        ])->first();

        $this->assertNotNull($flight);

        // Check the flight itself
        $this->assertEquals('KAUS', $flight->dpt_airport_id);
        $this->assertEquals('KJFK', $flight->arr_airport_id);
        $this->assertEquals('0810 CST', $flight->dpt_time);
        $this->assertEquals('1235 EST', $flight->arr_time);
        $this->assertEquals('350', $flight->level);
        $this->assertEquals(1477, $flight->distance->internal());
        $this->assertEquals('207', $flight->flight_time);
        $this->assertEquals(FlightType::SCHED_PAX, $flight->flight_type);
        $this->assertEquals('ILEXY2 ZENZI LFK ELD J29 MEM Q29 JHW J70 STENT J70 MAGIO J70 LVZ LENDY6', $flight->route);
        $this->assertEquals('Just a flight', $flight->notes);
        $this->assertEquals(true, $flight->active);

        // Test that the days were set properly
        $this->assertTrue($flight->on_day(Days::MONDAY));
        $this->assertTrue($flight->on_day(Days::FRIDAY));
        $this->assertFalse($flight->on_day(Days::TUESDAY));

        // Check the custom fields entered
        $fields = FlightFieldValue::where([
            'flight_id' => $flight->id,
        ])->get();

        $this->assertCount(2, $fields);
        $dep_gate = $fields->where('name', 'Departure Gate')->first();
        $this->assertEquals('4', $dep_gate['value']);

        $dep_gate = $fields->where('name', 'Arrival Gate')->first();
        $this->assertEquals('C41', $dep_gate['value']);

        // Check the fare class
        $fares = $this->fareSvc->getFareWithOverrides(null, $flight->fares);
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
        $this->assertNotEquals('A32X', $subfleets[0]->name);

        $flight = Flight::where([
            'airline_id'    => $airline->id,
            'flight_number' => '999',
        ])->first();
        $subfleets = $flight->subfleets;
        $this->assertCount(2, $subfleets);
        $this->assertEquals('B737', $subfleets[1]->type);
        $this->assertEquals('B737', $subfleets[1]->name);
    }

    /**
     * Test the flight importer
     *
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
        $flight = Flight::where([
            'airline_id'    => $airline->id,
            'flight_number' => '1972',
        ])->first();

        $this->assertNotNull($flight);

        // Check the custom fields entered
        $fields = FlightFieldValue::where([
            'flight_id' => $flight->id,
        ])->get();

        $this->assertCount(0, $fields);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testAircraftImporter(): void
    {
        Airline::factory()->create();
        // $subfleet = \App\Models\Subfleet::factory()->create(['type' => 'A32X']);

        $file_path = base_path('tests/data/aircraft.csv');
        $status = $this->importSvc->importAircraft($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

        // See if it imported
        $aircraft = Aircraft::where([
            'registration' => 'N309US',
        ])->first();

        $this->assertNotNull($aircraft);
        $this->assertNotNull($aircraft->hex_code);
        $this->assertNotNull($aircraft->subfleet);
        $this->assertNotNull($aircraft->subfleet->airline);
        $this->assertEquals('A32X', $aircraft->subfleet->type);
        $this->assertEquals('A320-211', $aircraft->name);
        $this->assertEquals('N309US', $aircraft->registration);
        $this->assertEquals(null, $aircraft->zfw);
        $this->assertEquals(AircraftStatus::ACTIVE, $aircraft->status);

        // Now try importing the updated file, the status for the aircraft should change
        // to being stored

        $file_path = base_path('tests/data/aircraft-update.csv');
        $status = $this->importSvc->importAircraft($file_path);
        $this->assertCount(1, $status['success']);

        $aircraft = Aircraft::where([
            'registration' => 'N309US',
        ])->first();

        $this->assertEquals(AircraftStatus::STORED, $aircraft->status);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testAirportImporter(): void
    {
        $file_path = base_path('tests/data/airports.csv');
        $status = $this->importSvc->importAirports($file_path);

        $this->assertCount(2, $status['success']);
        $this->assertCount(1, $status['errors']);

        // See if it imported
        $airport = Airport::where([
            'id' => 'KAUS',
        ])->first();

        $this->assertNotNull($airport);
        $this->assertEquals('KAUS', $airport->id);
        $this->assertEquals('AUS', $airport->iata);
        $this->assertEquals('KAUS', $airport->icao);
        $this->assertEquals('Austin-Bergstrom', $airport->name);
        $this->assertEquals('Austin, Texas, USA', $airport->location);
        $this->assertEquals('United States', $airport->country);
        $this->assertEquals('America/Chicago', $airport->timezone);
        $this->assertEquals(true, $airport->hub);
        $this->assertEquals('30.1945', $airport->lat);
        $this->assertEquals('-97.6699', $airport->lon);
        $this->assertEquals(0.0, $airport->ground_handling_cost);
        $this->assertEquals(setting('airports.default_jet_a_fuel_cost'), $airport->fuel_jeta_cost);
        $this->assertEquals('Test Note', $airport->notes);

        // See if it imported
        $airport = Airport::where([
            'id' => 'KSFO',
        ])->first();

        $this->assertNotNull($airport);
        $this->assertEquals(true, $airport->hub);
        $this->assertEquals(0.9, $airport->fuel_jeta_cost);
        $this->assertEquals(setting('airports.default_ground_handling_cost'), $airport->ground_handling_cost);
    }

    /**
     * Test importing the subfleets
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function testSubfleetImporter(): void
    {
        $fare_economy = Fare::factory()->create(['code' => 'Y', 'capacity' => 150]);
        $fare_business = Fare::factory()->create(['code' => 'B', 'capacity' => 20]);
        $airline = Airline::factory()->create(['icao' => 'VMS']);

        $file_path = base_path('tests/data/subfleets.csv');
        $status = $this->importSvc->importSubfleets($file_path);

        $this->assertCount(1, $status['success']);
        $this->assertCount(1, $status['errors']);

        // See if it imported
        $subfleet = Subfleet::where([
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

    public function testAirportSpecialCharsImporter(): void
    {
        $file_path = base_path('tests/data/airports_special_chars.csv');
        $status = $this->importSvc->importAirports($file_path);

        // See if it imported
        $airport = Airport::where([
            'id' => 'LEMD',
        ])->first();

        $this->assertNotNull($airport);
        $this->assertEquals('Adolfo Suárez Madrid–Barajas Airport', $airport->name);
    }
}
