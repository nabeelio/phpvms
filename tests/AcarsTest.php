<?php

use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Repositories\SettingRepository;

/**
 * Test API calls and authentication, etc
 */
class AcarsTest extends TestCase
{
    protected $settingsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->addData('base');

        $this->settingsRepo = app(SettingRepository::class);
    }

    /**
     * @param $route
     * @param $points
     * @param array $addtl_fields
     */
    protected function allPointsInRoute($route, $points, $addtl_fields = [])
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

        $this->assertEquals(\count($route), \count($points));
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

    protected function getPirep($pirep_id)
    {
        $resp = $this->get('/api/pireps/'.$pirep_id);
        $resp->assertStatus(200);

        return $resp->json()['data'];
    }

    /**
     * Test some prefile error conditions
     */
    public function testPrefileErrors()
    {
        $this->user = factory(App\Models\User::class)->create();

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = factory(App\Models\Aircraft::class)->create();

        /**
         * INVALID AIRLINE_ID FIELD
         */
        $uri = '/api/pireps/prefile';
        $pirep = [
            '_airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(400);

    }

    /**
     * Make sure an error is thrown if the pilot is not at the current airport
     */
    public function testPilotNotAtAirport(): void
    {
        $this->settingsRepo->store('pilots.only_flights_from_current', true);
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);

        $this->user = factory(App\Models\User::class)->create([
            'curr_airport_id' => 'KJFK',
        ]);

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = factory(App\Models\Aircraft::class)->create();

        /**
         * INVALID AIRLINE_ID FIELD
         */
        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id'          => $airline->id,
            'aircraft_id'         => $aircraft->id,
            'dpt_airport_id'      => $airport->icao,
            'arr_airport_id'      => $airport->icao,
            'flight_number'       => '6000',
            'level'               => 38000,
            'planned_flight_time' => 120,
            'route'               => 'POINTA POINTB',
            'source_name'         => 'phpunit',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(400);
        $body = $response->json();
        $this->assertEquals(\App\Exceptions\UserNotAtAirport::MESSAGE, $body['error']['message']);
    }

    /**
     * Make sure an error is thrown if the pilot is not at the current airport
     */
    public function testAircraftNotAtAirport(): void
    {
        $this->settingsRepo->store('pireps.only_aircraft_at_dpt_airport', true);
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);

        $this->user = factory(App\Models\User::class)->create([
            'curr_airport_id' => 'KJFK',
        ]);

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = factory(App\Models\Aircraft::class)->create([
            'airport_id' => 'KAUS'
        ]);

        /**
         * INVALID AIRLINE_ID FIELD
         */
        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id'          => $airline->id,
            'aircraft_id'         => $aircraft->id,
            'dpt_airport_id'      => $airport->icao,
            'arr_airport_id'      => $airport->icao,
            'flight_number'       => '6000',
            'level'               => 38000,
            'planned_flight_time' => 120,
            'route'               => 'POINTA POINTB',
            'source_name'         => 'phpunit',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(400);
        $body = $response->json();
        $this->assertEquals(\App\Exceptions\AircraftNotAtAirport::MESSAGE, $body['error']['message']);
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testPrefileAndUpdates()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $fare = factory(App\Models\Fare::class)->create();

        $this->user = factory(App\Models\User::class)->create(
            [
                'rank_id' => $rank->id,
            ]
        );

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = $subfleet['aircraft']->random();

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
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
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);
        $pirep = $response->json('data');

        # See that the fields and fares were set
        $fares = \App\Models\PirepFare::where('pirep_id', $pirep['id'])->get();
        $this->assertCount(1, $fares);
        $saved_fare = $fares->first();

        $this->assertEquals($fare->id, $saved_fare['fare_id']);
        $this->assertEquals($fare->capacity, $saved_fare['count']);

        # Check saved fields
        $saved_fields = \App\Models\PirepFieldValues::where('pirep_id', $pirep['id'])->get();
        $this->assertCount(1, $saved_fields);
        $field = $saved_fields->first();

        $this->assertEquals('custom_field', $field['name']);
        $this->assertEquals('custom_value', $field['value']);

        /**
         * Try to update fields
         */
        $uri = '/api/pireps/'.$pirep['id'].'/update';
        $update = [
            'fares' => [
                [
                    'id' => $fare->id,
                    'count' => $fare->capacity,
                ],
            ],
        ];

        $response = $this->post($uri, $update);
        $response->assertStatus(200);
        $updated_pirep = $response->json('data');

        # Make sure there are no duplicates
        $fares = \App\Models\PirepFare::where('pirep_id', $pirep['id'])->get();
        $this->assertCount(1, $fares);
        $saved_fare = $fares->first();

        $this->assertEquals($fare->id, $saved_fare['fare_id']);
        $this->assertEquals($fare->capacity, $saved_fare['count']);
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testAcarsUpdates()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        $this->user = factory(App\Models\User::class)->create(
            [
                'rank_id' => $rank->id,
            ]
        );

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = $subfleet['aircraft']->random();

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_distance' => 400,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'AcarsTest::testAcarsUpdates',
            'fields' => [
                'custom_field' => 'custom_value',
            ],
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);

        # Get the PIREP ID
        $body = $response->json();
        $pirep_id = $body['data']['id'];

        $this->assertHasKeys($body['data'], ['airline', 'arr_airport', 'dpt_airport', 'position']);
        $this->assertNotNull($pirep_id);
        $this->assertEquals($body['data']['user_id'], $this->user->id);

        # Check the PIREP state and status
        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::INITIATED, $pirep['status']);

        /**
         * Check the fields
         */
        $this->assertHasKeys($pirep, ['fields']);
        $this->assertEquals('custom_field', $pirep['fields'][0]['name']);
        $this->assertEquals('custom_value', $pirep['fields'][0]['value']);

        $this->assertHasKeys($pirep['planned_distance'], ['mi', 'nmi', 'km']);

        /**
         * Update the custom field
         */
        $uri = '/api/pireps/'.$pirep_id.'/update';
        $this->post(
            $uri,
            [
                'fields' => [
                    'custom_field' => 'custom_value_changed',
                ],
            ]
        );

        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals('custom_value_changed', $pirep['fields'][0]['value']);

        /**
         * Add some position updates
         */

        $uri = '/api/pireps/'.$pirep_id.'/acars/position';

        # Test missing positions field
        # Post an ACARS update
        $update = [];
        $response = $this->post($uri, $update);
        $response->assertStatus(400);

        # Post an ACARS update
        $acars = factory(App\Models\Acars::class)->make(['pirep_id' => $pirep_id])->toArray();

        $acars = $this->transformData($acars);

        $update = ['positions' => [$acars]];
        $response = $this->post($uri, $update);
        $response->assertStatus(200)->assertJson(['count' => 1]);

        # Read that if the ACARS record posted
        $acars_data = $this->get($uri)->json()['data'][0];
        $this->assertEquals(round($acars['lat'], 2), round($acars_data['lat'], 2));
        $this->assertEquals(round($acars['lon'], 2), round($acars_data['lon'], 2));
        $this->assertEquals($acars['log'], $acars_data['log']);

        # Make sure PIREP state moved into ENROUTE
        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::AIRBORNE, $pirep['status']);

        $response = $this->get($uri);
        $response->assertStatus(200);
        $body = $response->json()['data'];

        $this->assertNotNull($body);
        $this->assertCount(1, $body);
        $this->assertEquals(round($acars['lat'], 2), round($body[0]['lat'], 2));
        $this->assertEquals(round($acars['lon'], 2), round($body[0]['lon'], 2));

        # File the PIREP now
        $uri = '/api/pireps/'.$pirep_id.'/file';
        $response = $this->post($uri, []);
        $response->assertStatus(400); // missing field

        $response = $this->post($uri, ['flight_time' => '1:30']);
        $response->assertStatus(400); // invalid flight time

        $response = $this->post(
            $uri,
            [
                'flight_time' => 130,
                'fuel_used' => 8000.19,
                'distance' => 400,
            ]
        );

        $response->assertStatus(200);
        $body = $response->json();

        # Add a comment
        $uri = '/api/pireps/'.$pirep_id.'/comments';
        $response = $this->post($uri, ['comment' => 'A comment']);
        $response->assertStatus(201);

        $response = $this->get($uri);
        $response->assertStatus(200);
        $comments = $response->json();

        $this->assertCount(1, $comments);
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testFilePirepApi(): void
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        $this->user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = $subfleet['aircraft']->random();

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id'          => $airline->id,
            'aircraft_id'         => $aircraft->id,
            'dpt_airport_id'      => $airport->icao,
            'arr_airport_id'      => $airport->icao,
            'flight_number'       => '6000',
            'level'               => 38000,
            'source_name'         => 'AcarsTest::testFilePirepApi',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);

        # Get the PIREP ID
        $body = $response->json();
        $pirep_id = $body['data']['id'];

        # File the PIREP now
        $uri = '/api/pireps/'.$pirep_id.'/file';

        $response = $this->post($uri, [
            'flight_time' => 130,
            'fuel_used'   => 8000.19,
            'distance'    => 400,
        ]);

        $response->assertStatus(200);

        # Check the block_off_time and block_on_time being set
        $body = $this->get('/api/pireps/'.$pirep_id)->json('data');
        $this->assertNotNull($body['block_off_time']);
        $this->assertNotNull($body['block_on_time']);

        # make sure the time matches up
        /*$block_on = new Carbon($body['block_on_time'], 'UTC');
        $block_off = new Carbon($body['block_off_time'], 'UTC');
        $this->assertEquals($block_on->subMinutes($body['flight_time']), $block_off);*/
    }

    /**
     * Test aircraft is allowed
     */
    public function testAircraftAllowed()
    {
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', true);

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();

        # Add subfleets and aircraft, but also add another set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft(1);

        // User not allowed aircraft from this subfleet
        $subfleetB = $this->createSubfleetWithAircraft(1);

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);

        $this->user = factory(App\Models\User::class)->create(
            [
                'rank_id' => $rank->id,
            ]
        );

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $subfleetB['aircraft']->random()->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'Unit test',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(400);

        // Try refiling with a valid aircraft
        $pirep['aircraft_id'] = $subfleetA['aircraft']->random()->id;
        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);
    }

    /**
     * Test aircraft permissions being ignored
     */
    public function testIgnoreAircraftAllowed()
    {
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);

        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();

        # Add subfleets and aircraft, but also add another set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft(1);

        // User not allowed aircraft from this subfleet
        $subfleetB = $this->createSubfleetWithAircraft(1);

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);

        $this->user = factory(App\Models\User::class)->create(
            [
                'rank_id' => $rank->id,
            ]
        );

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $subfleetB['aircraft']->random()->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
            'source_name' => 'Unit test',
        ];

        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);
    }

    /**
     * Test publishing multiple, batched updates
     */
    public function testMultipleAcarsPositionUpdates()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);

        $pirep_id = $response->json()['data']['id'];

        $uri = '/api/pireps/'.$pirep_id.'/acars/position';

        # Post an ACARS update
        $acars_count = \random_int(2, 10);
        $acars = factory(App\Models\Acars::class, $acars_count)->make(['id' => ''])->toArray();

        $update = ['positions' => $acars];
        $response = $this->post($uri, $update);
        $response->assertStatus(200)->assertJson(['count' => $acars_count]);

        $response = $this->get($uri);
        $response->assertStatus(200)->assertJsonCount($acars_count, 'data');
    }

    /**
     *
     */
    public function testNonExistentPirepGet()
    {
        $this->user = factory(App\Models\User::class)->create();

        $uri = '/api/pireps/DOESNTEXIST/acars';
        $response = $this->get($uri);
        $response->assertStatus(404);
    }

    /**
     *
     */
    public function testNonExistentPirepStore()
    {
        $this->user = factory(App\Models\User::class)->create();

        $uri = '/api/pireps/DOESNTEXIST/acars/position';
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        $response = $this->post($uri, $acars);
        $response->assertStatus(404);
    }

    /**
     *
     */
    public function testAcarsIsoDate()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $pirep_id = $response->json()['data']['id'];

        $dt = date('c');
        $uri = '/api/pireps/'.$pirep_id.'/acars/position';
        $acars = factory(App\Models\Acars::class)->make(
            [
                'sim_time' => $dt,
            ]
        )->toArray();

        $update = ['positions' => [$acars]];
        $response = $this->post($uri, $update);
        $response->assertStatus(200);
    }

    /**
     * Test the validation
     */
    public function testAcarsInvalidRoutePost()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $pirep_id = $response->json()['data']['id'];

        $post_route = ['order' => 1, 'name' => 'NAVPOINT'];
        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->post($uri, $post_route);
        $response->assertStatus(400);

        $post_route = [
            ['order' => 1, 'name' => 'NAVPOINT', 'lat' => 'notanumber', 'lon' => 34.11],
        ];

        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->post($uri, $post_route);
        $response->assertStatus(400);
    }

    public function testAcarsLogPost()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $pirep_id = $response->json()['data']['id'];

        $acars = factory(App\Models\Acars::class)->make();
        $post_log = [
            'logs' => [
                ['log' => $acars->log],
            ],
        ];

        $uri = '/api/pireps/'.$pirep_id.'/acars/logs';
        $response = $this->post($uri, $post_log);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertEquals(1, $body['count']);

        $acars = factory(App\Models\Acars::class)->make();
        $post_log = [
            'events' => [
                ['event' => $acars->log],
            ],
        ];

        $uri = '/api/pireps/'.$pirep_id.'/acars/events';
        $response = $this->post($uri, $post_log);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertEquals(1, $body['count']);
    }

    /**
     *
     */
    public function testAcarsRoutePost()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $pirep_id = $response->json()['data']['id'];

        $order = 1;
        $post_route = [];
        $route_count = \random_int(2, 10);

        $route = factory(App\Models\Navdata::class, $route_count)->create();
        foreach ($route as $position) {
            $post_route[] = [
                'order' => $order,
                'id' => $position->id,
                'name' => $position->id,
                'lat' => $position->lat,
                'lon' => $position->lon,
            ];

            ++$order;
        }

        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->post($uri, ['route' => $post_route]);
        $response->assertStatus(200)->assertJson(['count' => $route_count]);

        /**
         * Get
         */

        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->get($uri);
        $response->assertStatus(200)->assertJsonCount($route_count, 'data');

        $body = $response->json()['data'];
        $this->allPointsInRoute($post_route, $body);

        /**
         * Delete and then recheck
         */
        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->delete($uri);
        $response->assertStatus(200);

        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->get($uri);
        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    /**
     * Try to refile the same PIREP
     */
    public function testDuplicatePirep()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $response->assertStatus(201);
        $pirep_id = $response->json()['data']['id'];

        # try readding
        $response = $this->post($uri, $pirep);
        $response->assertStatus(200);
        $dupe_pirep_id = $response->json()['data']['id'];

        $this->assertEquals($pirep_id, $dupe_pirep_id);
    }
}
