<?php

#use Swagger\Serializer;
use App\Services\FareService;
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

        $uri = '/api/user';

        // Missing auth header
        $this->get($uri)->assertStatus(401);

        // Test invalid API key
        $this->withHeaders(['Authorization' => 'invalidKey'])->get($uri)
            ->assertStatus(401);

        $this->withHeaders(['Authorization' => ''])->get($uri)
            ->assertStatus(401);

        // Test upper/lower case of Authorization header, etc
        $response = $this->get($uri, $this->headers($user));
        $response->assertStatus(200)->assertJson(['id' => $user->id], true);

        $this->withHeaders(['x-api-key' => $user->api_key])->get($uri)
            ->assertJson(['id' => $user->id], true);

        $this->withHeaders(['x-API-key' => $user->api_key])->get($uri)
            ->assertJson(['id' => $user->id], true);

        $this->withHeaders(['X-API-KEY' => $user->api_key])->get($uri)
            ->assertJson(['id' => $user->id], true);
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
     *
     */
    public function testGetAirlines()
    {
        $size = \random_int(5, 10);
        $this->user = factory(App\Models\User::class)->create([
            'airline_id' => 0
        ]);

        $airlines = factory(App\Models\Airline::class, $size)->create();

        $res = $this->get('/api/airlines');
        $body = $res->json();

        $this->assertCount($size, $body['data']);

        $airline = $airlines->random();
        $this->get('/api/airlines/'.$airline->id)->assertJson(['name' => $airline->name]);
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
        $this->user = factory(App\Models\User::class)->create();
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
        $this->user = factory(App\Models\User::class)->create();
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
        $this->user = factory(App\Models\User::class)->create();

        $subfleetA = factory(App\Models\Subfleet::class)->create([
            'airline_id' => $this->user->airline_id,
        ]);

        $subfleetB = factory(App\Models\Subfleet::class)->create([
            'airline_id' => $this->user->airline_id,
        ]);

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
        $this->user = factory(App\Models\User::class)->create();

        $fare_svc = app(FareService::class);

        $subfleet = factory(App\Models\Subfleet::class)->create([
            'airline_id' => $this->user->airline_id
        ]);

        $fare = factory(App\Models\Fare::class)->create();

        $fare_svc->setForSubfleet($subfleet, $fare);
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

    public function testGetAllSettings()
    {
        $this->user = factory(App\Models\User::class)->create();
        $res = $this->get('/api/settings')->assertStatus(200);
        $settings = $res->json();
    }
}
