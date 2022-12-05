<?php

namespace Tests;

use App\Exceptions\AircraftNotAtAirport;
use App\Exceptions\UserNotAtAirport;
use App\Models\Acars;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Fare;
use App\Models\Navdata;
use App\Models\PirepFare;
use App\Models\PirepFieldValue;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;

use function count;
use function random_int;

/**
 * Test API calls and authentication, etc
 */
class AcarsTest extends TestCase
{
    protected SettingRepository $settingsRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');

        $this->settingsRepository = app(SettingRepository::class);
    }

    protected function createPirepResponse(array $data = []): TestResponse
    {
        return $this->post('/api/pireps/prefile', $data);
    }

    protected function allPointsInRoute(array $route, array $points, array $addtl_fields = []): void
    {
        if (empty($addtl_fields)) {
            $addtl_fields = [];
        }

        $fields = array_merge(
            [
                'name',
                'order',
                'lat',
                'lon',
            ],
            $addtl_fields
        );

        $this->assertCount(count($route), $points);
        foreach ($route as $idx => $point) {
            $this->assertHasKeys($points[$idx], $fields);
            foreach ($fields as $f) {
                if ($f === 'lat' || $f === 'lon') {
                    continue;
                }

                $this->assertEquals($point[$f], $points[$idx][$f]);
            }
        }
    }

    protected function getPirep(string $pirep_id): array
    {
        return $this->get('/api/pireps/' . $pirep_id)->assertOk()->json('data');
    }

    /**
     * Test some prefile error conditions
     */
    public function testPrefileErrors()
    {
        $this->user = User::factory()->create();

        /**
         * INVALID AIRLINE_ID FIELD
         */
        $this->createPirepResponse([
            '_airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => Aircraft::factory()->create()->id,
            'dpt_airport_id' =>  $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB'
        ])
            ->assertJsonValidationErrorFor('airline_id')
            ->assertStatus(400);
    }

    public function testPrefileAircraftMustBeAtAirport()
    {
        $this->user = User::factory()->create();

        $this->settingsRepository->store('pilots.only_flights_from_current', false);
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', false);
        $this->settingsRepository->store('pireps.only_aircraft_at_dpt_airport', true);

        $airportICAO = Airport::factory()->create()->icao;
        $aircraftID = Aircraft::factory()->create(['airport_id' => Airport::factory()->create()->id])->id;

        $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $aircraftID,
            'dpt_airport_id' => $airportICAO,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'Tests'
        ])->assertStatus(400)
            ->assertJsonPath('title', 'The aircraft is not at the departure airport');
    }

    public function testAirportFieldsCannotBeBlank()
    {
        $this->user = User::factory()->create();

        $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => Aircraft::factory()->create()->id,
            'dpt_airport_id' => null,
            'arr_airport_id' => null,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'source_name' => 'ACARSTESTS',
            'route' => 'POINTA POINTB',
        ])->assertStatus(400)
            ->assertJsonValidationErrors(['dpt_airport_id', 'arr_airport_id'])
            ->assertJsonPath('details', 'A departure airport is required, An arrival airport is required');
    }

    /**
     * Make sure an error is thrown if the pilot is not at the current airport
     */
    public function testPilotMustBeAtCurrentAirport(): void
    {
        $this->settingsRepository->store('pilots.only_flights_from_current', true);
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', false);

        $this->user = User::factory()->create(['curr_airport_id' => 'KJFK']);

        $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => Aircraft::factory()->create()->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'phpunit',
        ])->assertStatus(400)
            ->assertJsonPath('error.message', UserNotAtAirport::MESSAGE);
    }

    /**
     * Make sure an error is thrown if the aircraft is not at the current airport
     */
    public function testAircraftMustBeAtAirport(): void
    {
        $this->settingsRepository->store('pireps.only_aircraft_at_dpt_airport', true);
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', false);
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', false);

        /** @var User user */
        $this->user = User::factory()->create(['curr_airport_id' => 'KJFK']);

        $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => Aircraft::factory()->create(['airport_id' => 'KAUS'])->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'phpunit',
        ])->assertStatus(400)
            ->assertJsonPath('error.message', AircraftNotAtAirport::MESSAGE);
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testPrefileAndUpdates()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        /** @var Fare $fare */
        $fare = Fare::factory()->create();

        /** @var Aircraft $aircraft */
        $aircraft = $subfleet['aircraft']->random();

        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        $pirepID = $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_distance' => 400,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'UnitTest',
            'fields' => [
                'custom_field' => 'custom_value',
            ],
            'fares' => [
                [
                    'id' => $fare->id,
                    'count' => $fare->capacity,
                ],
            ],
        ])->assertOk()
            ->assertJsonPath('data.planned_distance.nmi', 400)
            ->assertJsonPath('data.planned_distance.mi', 460.31)
            ->assertJsonPath('data.planned_distance.km', 740.8)
            ->assertJsonPath('data.planned_distance.m', 740800)
            ->assertJsonPath('data.submitted_at', fn ($dataTime) => str_ends_with($dataTime, 'Z')) // Are date times in UTC?
            ->json('data.id');

        // See that the fields and fares were set
        $saved_fare = PirepFare::query()->where('pirep_id', $pirepID)->sole(['fare_id', 'count']); //throws exception if result is greater than one.
        $this->assertEquals($fare->id, $saved_fare['fare_id']);
        $this->assertEquals($fare->capacity, $saved_fare['count']);

        // Check saved fields
        $field = PirepFieldValue::query()->where('pirep_id', $pirepID)->sole(['name', 'value']);
        $this->assertEquals('custom_field', $field['name']);
        $this->assertEquals('custom_value', $field['value']);

        //Try to update fields
        $this->post('/api/pireps/' . $pirepID . '/update', [
            'fares' => [
                [
                    'id'    => $fare->id,
                    'count' => $fare->capacity,
                ],
            ],
        ])->assertOk();

        // Make sure there are no duplicates
        $saved_fare = PirepFare::query()->where('pirep_id', $pirepID)->sole(['fare_id', 'count']);
        $this->assertEquals($fare->id, $saved_fare['fare_id']);
        $this->assertEquals($fare->capacity, $saved_fare['count']);

        //Try cancelling the PIREP now
        $this->put('/api/pireps/' . $pirepID . '/cancel', [])->assertOk();

        // Read it
        $this->get('/api/pireps/' . $pirepID)
            ->assertOk()
            ->assertJsonPath('data.state', PirepState::CANCELLED);
    }

    public function testPrefileAndInvalidUpdates()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        /** @var Aircraft $aircraft */
        $aircraft = $subfleet['aircraft']->random();

        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        $pirep = $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_distance' => 400,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'UnitTest',
        ])->assertStatus(200)
            ->json('data');

        //Try to update fields
        $this->post('/api/pireps/' . $pirep['id'] . '/update', ['dpt_airport_id' => ''])
            ->assertStatus(400)
            ->assertJsonPath('details', 'A departure airport is required')
            ->json('details');
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     * @throws \Exception
     */
    public function testAcarsUpdates()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        /** @var Aircraft $aircraft */
        $aircraft = $subfleet['aircraft']->random();

        /** @var User user */
        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        $pirep_create = [
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_distance' => 400,
            'planned_flight_time' => 120,
            'status' => PirepStatus::BOARDING,
            'route' => 'POINTA POINTB',
            'source_name' => 'AcarsTest::testAcarsUpdates',
            'fields' => [
                'custom_field' => 'custom_value',
            ],
        ];

        $pirep_id = $this->createPirepResponse($pirep_create)
            ->assertStatus(200)
            ->assertJsonPath('data', function (array $data) {
                $this->assertHasKeys($data, ['airline', 'arr_airport', 'dpt_airport']);
                return true;
            })
            ->assertJsonPath('data.id', fn (?string $id) => $id !== null)
            ->assertJsonPath('data.user_id', $this->user->id)
            ->json('data.id');

        $pirep = $this->getPirep($pirep_id);

        // Check the PIREP state and status
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::INITIATED, $pirep['status']);

        //Check the fields
        $this->assertHasKeys($pirep, ['fields']);
        $this->assertEquals('custom_value', $pirep['fields']['custom_field']);
        $this->assertEquals($pirep_create['planned_distance'], $pirep['planned_distance']['nmi']);
        $this->assertHasKeys($pirep['planned_distance'], ['mi', 'nmi', 'km']);

        //Update the custom field
        $this->post('/api/pireps/' . $pirep_id . '/update', [
            'flight_time' => 60,
            'distance' => 20,
            'status' => PirepStatus::AIRBORNE,
            'fields' => [
                'custom_field' => 'custom_value_changed',
            ],
        ]);

        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals('custom_value_changed', $pirep['fields']['custom_field']);

        //Add some position updates
        $uri = '/api/pireps/' . $pirep_id . '/acars/position';

        // Test missing positions field
        // Post an ACARS update
        $this->post($uri, [])->assertStatus(400);

        // Post an ACARS update
        $acars = Acars::factory()->make(['pirep_id' => $pirep_id])->toArray();
        $acars = $this->transformData($acars);

        $this->post($uri, ['positions' => [$acars]])->assertStatus(200)->assertJson(['count' => 1]);

        // Read that if the ACARS record posted
        $acars_data = $this->get($uri)->json('data')[0];
        $this->assertEquals(round($acars['lat'], 2), round($acars_data['lat'], 2));
        $this->assertEquals(round($acars['lon'], 2), round($acars_data['lon'], 2));
        $this->assertEquals($acars['log'], $acars_data['log']);

        // Make sure PIREP state moved into ENROUTE
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::AIRBORNE, $pirep['status']);

        $this->get($uri)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.lat', fn (float $latitude) => round($latitude, 2) === round($acars['lat'], 2))
            ->assertJsonPath('data.0.lon', fn (float $longitude) => round($longitude, 2) === round($acars['lon'], 2));

        // Update fields standalone
        $this->post('/api/pireps/' . $pirep_id . '/fields', [
            'fields' => [
                'Departure Gate' => 'G26',
            ],
        ])->assertStatus(200)
            ->assertJsonPath('data.Departure Gate', 'G26');

        //Get the live flights and make sure all the fields we want are there
        $this->get('/api/acars')
            ->assertStatus(200)
            ->collect('data')
            ->filter(fn (array $data) => $data['id'] === $pirep['id'])
            ->tap(fn (Collection $collection) => $this->assertCount(1, $collection)) // assert not empty
            ->each(function (array $body) {
                $this->assertNotEmpty($body['user']['name']);
                $this->assertNotEmpty($body['user']['avatar']);
            });


        //File the PIREP
        $filePirepUri = '/api/pireps/' . $pirep_id . '/file';
        $this->post($filePirepUri, [])
            ->assertJsonValidationErrors(['flight_time'])
            ->assertStatus(400); // missing field

        $this->post($filePirepUri, ['flight_time' => '1:30'])
            ->assertJsonValidationErrors(['flight_time'])
            ->assertStatus(400); // invalid flight time

        $this->post($filePirepUri, [
            'flight_time' => 130,
            'fuel_used'   => 8000.19,
            'distance'    => 400,
        ])->assertOk();

        // Add a comment
        $commentUri = '/api/pireps/' . $pirep_id . '/comments';
        $this->post($commentUri, ['comment' => 'A comment'])->assertCreated();
        $this->get($commentUri)->assertOk(200)->assertJsonCount(1);
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testFilePirepApi(): void
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        /** @var Aircraft $aircraft */
        $aircraft = $subfleet['aircraft']->random();

        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        // Get the PIREP ID
        $pirep_id = $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number'  => '6000',
            'level' => 38000,
            'source_name' => 'AcarsTest::testFilePirepApi',
        ])->assertOk()
            ->json('data.id');

        // File the PIREP now
        $filePirepUri = '/api/pireps/' . $pirep_id . '/file';
        $this->post($filePirepUri, [
            'flight_time' => 130,
            'fuel_used'   => 8000.19,
            'distance'    => 400,
        ])->assertStatus(200);

        // Check the block_off_time and block_on_time being set
        $this->get('/api/pireps/' . $pirep_id)
            ->assertJsonPath('data.state', PirepState::PENDING)
            ->assertJsonPath('data.block_off_time', fn (?string $dateTime) => $dateTime !== null)
            ->assertJsonPath('data.block_on_time', fn (?string $dateTime) => $dateTime !== null);

        // Try to refile, should be blocked
        $this->post($filePirepUri, [
            'flight_time' => 130,
            'fuel_used'   => 8000.19,
            'distance'    => 400,
        ])->assertStatus(400);
    }

    /**
     * Test aircraft is allowed
     */
    public function testAircraftAllowed()
    {
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', true);

        // Add subfleets and aircraft, but also add another set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft(1);

        // User not allowed aircraft from this subfleet
        $subfleetB = $this->createSubfleetWithAircraft(1);

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);
        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        $data = [
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $subfleetB['aircraft']->random()->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'Unit test',
        ];

        $this->createPirepResponse($data)->assertStatus(400);

        // Try refiling with a valid aircraft
        $data['aircraft_id'] = $subfleetA['aircraft']->random()->id;
        $this->createPirepResponse($data)->assertOk();
    }

    /**
     * Test aircraft permissions being ignored
     */
    public function testIgnoreAircraftAllowed()
    {
        $this->settingsRepository->store('pireps.restrict_aircraft_to_rank', false);

        // Add subfleets and aircraft, but also add another set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft(1);

        // User not allowed aircraft from this subfleet
        $subfleetB = $this->createSubfleetWithAircraft(1);

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);

        $this->user = User::factory()->create(['rank_id' => $rank->id]);

        $this->createPirepResponse([
            'airline_id' => Airline::factory()->create()->id,
            'aircraft_id' => $subfleetB['aircraft']->random()->id,
            'dpt_airport_id' => $airportICAO = Airport::factory()->create()->icao,
            'arr_airport_id' => $airportICAO,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'Unit test',
        ])->assertOk();
    }

    /**
     * Test publishing multiple, batched updates
     *
     * @throws Exception
     */
    public function testMultipleAcarsPositionUpdates()
    {
        $pirep_id = $this->createPirepResponse($this->createPirep()->toArray())
            ->assertStatus(200)
            ->json('data.id');

        $uri = '/api/pireps/' . $pirep_id . '/acars/position';

        // Post an ACARS update
        $acars_count = random_int(5, 10);
        $acars = Acars::factory()
            ->count($acars_count)
            ->make(['id' => ''])
            ->map(function ($point) {
                $point['id'] = Utils::generateNewId();
                return $point;
            })
            ->toArray();

        $this->post($uri, $data = ['positions' => $acars])
            ->assertStatus(200)
            ->assertJson(['count' => $acars_count]);

        // Try posting again, should be ignored/not throw any sql errors
        $this->post($uri, $data)
            ->assertStatus(200)
            ->assertJson(['count' => $acars_count]);

        $this->get($uri)
            ->assertStatus(200)
            ->assertJsonCount($acars_count, 'data');
    }

    public function testNonExistentPirepGet()
    {
        $this->user = User::factory()->create();

        $this->get('/api/pireps/DOESNTEXIST/acars')->assertNotFound();
    }

    public function testNonExistentPirepStore()
    {
        $this->user = User::factory()->create();

        $acars = Acars::factory()->make()->toArray();
        $this->post('/api/pireps/DOESNTEXIST/acars/position', $acars)->assertNotFound();
    }

    public function testAcarsIsoDate()
    {
        $pirep_id = $this->createPirepResponse($this->createPirep()->toArray())->assertOk()->json('data.id');
        $acars = Acars::factory()->make(['sim_time' => date('c')])->toArray();

        $this->post('/api/pireps/' . $pirep_id . '/acars/position', ['positions' => [$acars]])->assertOk();
    }

    /**
     * Test the validation
     */
    public function testAcarsInvalidRoutePost()
    {
        $pirep_id = $this->createPirepResponse($this->createPirep()->toArray())->assertOk()->json('data.id');

        // Missing lat/lon
        $uri = '/api/pireps/' . $pirep_id . '/route';
        $this->post($uri, ['order' => 1, 'name' => 'NAVPOINT'])->assertStatus(400);

        $this->post($uri, [
            [
                'id'    => 'NAVPOINT',
                'order' => 1,
                'name'  => 'NAVPOINT',
                'lat'   => 'notanumber',
                'lon'   => 34.11,
            ],
        ])->assertStatus(400);
    }

    /**
     * Test the validation
     */
    public function testAcarsLogPost()
    {
        $pirep_id = $this->createPirepResponse($this->createPirep()->toArray())->json('data.id');

        $this->post('/api/pireps/' . $pirep_id . '/acars/logs', [
            'logs' => [
                ['log' => Acars::factory()->make()->log],
            ],
        ])->assertOk()
            ->assertJsonPath('count', 1);

        $this->post('/api/pireps/' . $pirep_id . '/acars/events', [
            'events' => [
                ['event' => Acars::factory()->make()->log],
            ],
        ])->assertOk()
            ->assertJsonPath('count', 1);
    }

    public function testAcarsRoutePost()
    {
        $pirep_id = $this->createPirepResponse($this->createPirep()->toArray())->assertOk()->json('data.id');
        $uri = '/api/pireps/' . $pirep_id . '/route';

        $order = 1;
        $post_route = [];
        $route_count = random_int(2, 10);
        $route = Navdata::factory()->count($route_count)->create();

        foreach ($route as $position) {
            $post_route[] = [
                'order' => $order,
                'id'    => $position->id,
                'name'  => $position->id,
                'lat'   => $position->lat,
                'lon'   => $position->lon,
            ];

            $order++;
        }

        $this->post($uri, ['route' => $post_route])
            ->assertOk(200)
            ->assertJson(['count' => $route_count]);

        // Try double post to ignore SQL update
        $this->post($uri, ['route' => $post_route])
            ->assertStatus(200)
            ->assertJson(['count' => $route_count]);

        //Get
        $response = $this->get($uri)
            ->assertStatus(200)
            ->assertJsonCount($route_count, 'data');

        $this->allPointsInRoute($post_route, $response->json('data'));

        //Delete and then recheck
        $this->delete($uri)->assertStatus(200);

        $this->get($uri)
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Try to refile the same PIREP
     */
    public function testDuplicatePirep()
    {
        $data = $this->createPirep()->toArray();
        $pirep_id = $this->createPirepResponse($data)->assertOk()->json('data.id');

        // try readding
        $dupe_pirep_id = $this->createPirepResponse($data)->assertOk()->json('data.id');

        $this->assertEquals($pirep_id, $dupe_pirep_id);
    }
}
