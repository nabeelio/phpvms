<?php

#use Swagger\Serializer;
use App\Models\User;

/**
 * Test API calls and authentication, etc
 */
class ApiTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->addData('base');
    }

    /**
     * Ensure authentication against the API works
     */
    public function testApiAuthentication()
    {
        $user = factory(User::class)->create();
        $pirep = factory(App\Models\Pirep::class)->create();

        $uri = '/api/pireps/' . $pirep->id;

        // Missing auth header
        $this->get($uri)->assertStatus(401);

        // Test invalid API key
        $this->withHeaders(['Authorization' => 'invalidKey'])->get($uri)
            ->assertStatus(401);

        $this->withHeaders(['Authorization' => ''])->get($uri)
            ->assertStatus(401);

        // Test upper/lower case of Authorization header, etc
        $response = $this->withHeaders($this->apiHeaders())->get($uri);
        $response->assertStatus(200)->assertJson(['id' => $pirep->id], true);

        $this->withHeaders(['x-api-key' => $user->api_key])->get($uri)
            ->assertStatus(200)
            ->assertJson(['id' => $pirep->id], true);

        $this->withHeaders(['x-API-key' => $user->api_key])->get($uri)
            ->assertStatus(200)
            ->assertJson(['id' => $pirep->id], true);

        $this->withHeaders(['X-API-KEY' => $user->api_key])->get($uri)
            ->assertStatus(200)
            ->assertJson(['id' => $pirep->id], true);
    }

    /**
     *
     */
    public function testApiDeniedOnInactiveUser()
    {
        $this->user = factory(User::class)->create([
            'state' => UserState::PENDING
        ]);

        $uri = '/api/user';
        $this->get($uri)->assertStatus(401);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest()
    {
        $this->user = factory(App\Models\User::class)->create();
        $airport = factory(App\Models\Airport::class)->create();

        $response = $this->get('/api/airports/' . $airport->icao);

        $response->assertStatus(200);
        $response->assertJson(['icao' => $airport->icao], true);

        $this->get('/api/airports/UNK')->assertStatus(404);
    }

    /**
     * Get all the airports, test the pagination
     */
    public function testGetAllAirports()
    {
        factory(App\Models\Airport::class, 70)->create();

        $response = $this->get('/api/airports/')
                         ->assertStatus(200)
                         ->assertJsonCount(50, 'data');

        $body = $response->json();

        $this->assertHasKeys($body, ['data', 'links', 'meta']);

        $last_page = $body['meta']['last_page'];
        $this->get('/api/airports?page=' . $last_page)
             ->assertStatus(200)
             ->assertJsonCount(20, 'data');
    }

    public function testGetAllAirportsHubs()
    {
        factory(App\Models\Airport::class, 10)->create();
        factory(App\Models\Airport::class)->create(['hub' => 1]);

        $this->get('/api/airports/hubs')
             ->assertStatus(200)
             ->assertJsonCount(1, 'data');
    }

    /**
     * Test getting the subfleets
     */
    public function testGetSubfleets()
    {
        $subfleetA = factory(App\Models\Subfleet::class)->create();
        $subfleetB = factory(App\Models\Subfleet::class)->create();

        $subfleetA_size = \random_int(2, 10);
        $subfleetB_size = \random_int(2, 10);
        factory(App\Models\Aircraft::class, $subfleetA_size)->create([
            'subfleet_id' => $subfleetA->id
        ]);

        factory(App\Models\Aircraft::class, $subfleetB_size)->create([
            'subfleet_id' => $subfleetB->id
        ]);

        $response = $this->get('/api/fleet');
        $response->assertStatus(200);
        $body = $response->json();

        foreach($body['data'] as $subfleet) {
            if($subfleet['id'] === $subfleetA->id) {
                $size = $subfleetA_size;
            } else {
                $size = $subfleetB_size;
            }

            $this->assertCount($size, $subfleet['aircraft']);
        }
    }

    /**
     * Test getting an aircraft
     */
    public function testGetAircraft()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        $aircraft = factory(App\Models\Aircraft::class)->create([
            'subfleet_id' => $subfleet->id
        ]);

        /**
         * Just try retrieving by ID
         */
        $resp = $this->get('/api/fleet/aircraft/' . $aircraft->id);
        $body = $resp->json();
        $this->assertEquals($body['id'], $aircraft->id);

        $resp = $this->get('/api/fleet/aircraft/' . $aircraft->id . '?registration=' . $aircraft->registration);
        $body = $resp->json();
        $this->assertEquals($body['id'], $aircraft->id);

        $resp = $this->get('/api/fleet/aircraft/' . $aircraft->id . '?tail_number=' . $aircraft->registration);
        $body = $resp->json();
        $this->assertEquals($body['id'], $aircraft->id);

        $resp = $this->get('/api/fleet/aircraft/' . $aircraft->id . '?icao=' . $aircraft->icao);
        $body = $resp->json();
        $this->assertEquals($body['id'], $aircraft->id);
    }
}
