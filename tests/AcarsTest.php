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

    protected function getPirep($pirep_id)
    {
        $resp = $this->withHeaders($this->apiHeaders())
                ->get('/api/pirep/' . $pirep_id);
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

        $uri = '/api/pirep/prefile';
        $pirep = [
            'airline_id' => $airline->id,
            'aircraft_id' => $aircraft->id,
            'dpt_airport' => $airport->icao,
            'arr_airport' => $airport->icao,
            'altitude' => 38000,
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

        # Post an ACARS update
        $uri = '/api/pirep/' . $pirep_id . '/acars';
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $acars);
        $response->assertStatus(201);

        $body = $response->json();
        $this->assertNotNull($body['id']);
        $this->assertEquals($pirep_id, $body['pirep_id']);

        # Make sure PIREP state moved into ENROUTE
        $pirep = $this->getPirep($pirep_id);
        $this->assertEquals(PirepState::IN_PROGRESS, $pirep['state']);
        $this->assertEquals(PirepStatus::ENROUTE, $pirep['status']);

        $uri = '/api/pirep/' . $pirep_id . '/acars';
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200);

        $body = $response->json();
        $this->assertEquals(1, $this->count($body));
        $this->assertEquals($pirep_id, $body[0]['pirep_id']);
    }

    public function testNonExistentPirepGet()
    {
        $uri = '/api/pirep/DOESNTEXIST/acars';
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(404);
    }

    public function testNonExistentPirepStore()
    {
        $uri = '/api/pirep/DOESNTEXIST/acars';
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        $response = $this->withHeaders($this->apiHeaders())->post($uri, $acars);
        $response->assertStatus(404);
    }
}
