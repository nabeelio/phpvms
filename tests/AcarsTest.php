<?php

use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;

/**
 * Test API calls and authentication, etc
 */
class AcarsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->addData('base');
    }

    /**
     * @param $route
     * @param $points
     * @param array $addtl_fields
     */
    protected function allPointsInRoute($route, $points, $addtl_fields=[])
    {
        if(empty($addtl_fields)) {
            $addtl_fields = [];
        }

        $fields = array_merge([
            'name',
            'order',
            'lat',
            'lon'
        ], $addtl_fields);

        $this->assertEquals(\count($route), \count($points));
        foreach($route as $idx => $point) {
            //$this->assertHasKeys($points[$idx], $fields);
            foreach($fields as $f) {
                $this->assertEquals($point[$f], $points[$idx][$f]);
            }
        }
    }

    protected function getPirep($pirep_id)
    {
        $resp = $this->withHeaders($this->apiHeaders())
                ->get('/api/pireps/' . $pirep_id);
        $resp->assertStatus(200);
        return $resp->json();
    }

    /**
     * Post a PIREP into a PREFILE state and post ACARS
     */
    public function testAcarsUpdates()
    {
        $airport = factory(App\Models\Airport::class)->create();
        $airline = factory(App\Models\Airline::class)->create();
        $aircraft = factory(App\Models\Aircraft::class)->create();

        $uri = '/api/pireps/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport_id' => $airport->icao,
            'arr_airport_id' => $airport->icao,
            'flight_number' => '6000',
            'level' => 38000,
            'planned_flight_time' => 120,
            'route' => 'POINTA POINTB',
        ];

        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $response->assertStatus(201);

        # Get the PIREP ID
        $pirep_id = $response->json()['id'];
        $this->assertNotNull($pirep_id);

        # Check the PIREP state and status
        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::PREFILE, $pirep['status']);

        $uri = '/api/pireps/' . $pirep_id . '/acars/position';

        # Test missing positions field
        # Post an ACARS update
        $update = [];
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $update);
        $response->assertStatus(400);

        # Post an ACARS update
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        unset($acars['id']);

        $update = ['positions' => [$acars]];
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $update);
        $response->assertStatus(200)->assertJson(['count' => 1]);

        # Make sure PIREP state moved into ENROUTE
        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::ENROUTE, $pirep['status']);

        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertNotNull($body);
        $this->assertCount(1, $body);
        $this->assertEquals(round($acars['lat'], 2), round($body[0]['lat'], 2));
        $this->assertEquals(round($acars['lon'], 2), round($body[0]['lon'], 2));
    }

    /**
     * Test publishing multiple, batched updates
     */
    public function testMultipleAcarsPositionUpdates()
    {
        $pirep = factory(App\Models\Pirep::class)->make()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $response->assertStatus(201);

        $pirep_id = $response->json()['id'];

        $uri = '/api/pireps/' . $pirep_id . '/acars/position';

        # Post an ACARS update
        $acars_count = \random_int(5, 50);
        $acars = factory(App\Models\Acars::class, $acars_count)->make(['id'=>''])->toArray();

        $update = ['positions' => $acars];
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $update);
        $response->assertStatus(200)->assertJson(['count' => $acars_count]);

        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200)->assertJsonCount($acars_count);
    }

    /**
     *
     */
    public function testNonExistentPirepGet()
    {
        $uri = '/api/pireps/DOESNTEXIST/acars';
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(404);
    }

    /**
     *
     */
    public function testNonExistentPirepStore()
    {
        $uri = '/api/pireps/DOESNTEXIST/acars/position';
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $acars);
        $response->assertStatus(404);
    }

    /**
     *
     */
    public function testAcarsIsoDate()
    {
        $pirep = factory(App\Models\Pirep::class)->make()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $pirep_id = $response->json()['id'];

        $dt = date('c');
        $uri = '/api/pireps/' . $pirep_id . '/acars/position';
        $acars = factory(App\Models\Acars::class)->make([
            'sim_time' => $dt
        ])->toArray();

        $update = ['positions' => [$acars]];
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $update);
        $response->assertStatus(200);
    }

    /**
     * Test the validation
     */
    public function testAcarsInvalidRoutePost()
    {
        $pirep = factory(App\Models\Pirep::class)->make()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $pirep_id = $response->json()['id'];

        $post_route = ['order' => 1, 'name' => 'NAVPOINT'];
        $uri = '/api/pireps/' . $pirep_id . '/route';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $post_route);
        $response->assertStatus(400);

        $post_route = [
            ['order' => 1, 'name' => 'NAVPOINT', 'lat' => 'notanumber', 'lon' => 34.11]
        ];

        $uri = '/api/pireps/' . $pirep_id . '/route';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $post_route);
        $response->assertStatus(400);
    }

    public function testAcarsLogPost()
    {
        $pirep = factory(App\Models\Pirep::class)->make()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $pirep_id = $response->json()['id'];

        $acars = factory(App\Models\Acars::class)->make();
        $post_log = [
            'logs' => [
                ['log' => $acars->log]
            ]
        ];

        $uri = '/api/pireps/' . $pirep_id . '/acars/log';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $post_log);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertEquals(1, $body['count']);
    }

    /**
     *
     */
    public function testAcarsRoutePost()
    {
        $pirep = factory(App\Models\Pirep::class)->make()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $pirep_id = $response->json()['id'];

        $order = 1;
        $post_route = [];
        $route_count = \random_int(5, 50);

        $route = factory(App\Models\Navdata::class, $route_count)->create();
        foreach($route as $position) {
            $post_route[] = [
                'order' => $order,
                'name' => $position->name,
                'lat' => $position->lat,
                'lon' => $position->lon,
            ];

            ++$order;
        }

        $uri = '/api/pireps/'.$pirep_id.'/route';
        $response = $this->withHeaders($this->apiHeaders())->post($uri, ['route' => $post_route]);
        $response->assertStatus(200)->assertJsonCount($route_count);

        $body = $response->json();
        $this->allPointsInRoute($post_route, $body);

        /**
         * Get
         */

        $uri = '/api/pireps/' . $pirep_id . '/route';
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200)->assertJsonCount($route_count);
        $body = $response->json();
        $this->allPointsInRoute($post_route, $body);

        /**
         * Delete and then recheck
         */
        $uri = '/api/pireps/' . $pirep_id . '/route';
        $response = $this->withHeaders($this->apiHeaders())->delete($uri);
        $response->assertStatus(200);

        $uri = '/api/pireps/' . $pirep_id . '/route';
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200)->assertJsonCount(0);
    }

    /**
     * Try to refile the same PIREP
     */
    public function testDuplicatePirep()
    {
        $uri = '/api/pireps/prefile';
        $user = factory(App\Models\User::class)->create();
        $pirep = factory(App\Models\Pirep::class)->make([
            'id' => '',
            'airline_id' => $user->airline_id,
            'user_id' => $user->id,
        ])->toArray();

        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $response->assertStatus(201);
        $pirep = $response->json();

        $response = $this->withHeaders($this->apiHeaders())->post($uri, $pirep);
        $response->assertStatus(200);
        $body = $response->json();
    }
}
