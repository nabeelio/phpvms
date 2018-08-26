<?php

//use Swagger\Serializer;
use App\Models\User;
use App\Services\FareService;

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
        $body = $response->json();
        $response->assertStatus(200)->assertJson(['data' => ['id' => $user->id]]);

        $this->withHeaders(['x-api-key' => $user->api_key])->get($uri)
            ->assertJson(['data' => ['id' => $user->id]]);

        $this->withHeaders(['x-API-key' => $user->api_key])->get($uri)
            ->assertJson(['data' => ['id' => $user->id]]);

        $this->withHeaders(['X-API-KEY' => $user->api_key])->get($uri)
            ->assertJson(['data' => ['id' => $user->id]]);
    }

    public function testApiDeniedOnInactiveUser()
    {
        $this->user = factory(User::class)->create([
            'state' => UserState::PENDING,
        ]);

        $uri = '/api/user';
        $this->get($uri)->assertStatus(401);
    }

    /**
     * Test getting the news from the API
     */
    public function testGetNews(): void
    {
        factory(App\Models\News::class)->create();
        $response = $this->get('/api/news')->json();

        $this->assertCount(1, $response['data']);
        $this->assertTrue(array_key_exists('user', $response['data'][0]));
    }

    /**
     * @throws Exception
     */
    public function testGetAirlines()
    {
        $size = \random_int(5, 10);
        $this->user = factory(App\Models\User::class)->create([
            'airline_id' => 0,
        ]);

        $airlines = factory(App\Models\Airline::class, $size)->create();

        $res = $this->get('/api/airlines');
        $body = $res->json();

        $this->assertCount($size, $body['data']);

        $airline = $airlines->random();
        $this->get('/api/airlines/'.$airline->id)
             ->assertJson(['data' => ['name' => $airline->name]]);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest()
    {
        $this->user = factory(App\Models\User::class)->create();
        $airport = factory(App\Models\Airport::class)->create();

        $response = $this->get('/api/airports/'.$airport->icao);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['icao' => $airport->icao]]);

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
                         ->assertStatus(200);

        $body = $response->json();
        $this->assertHasKeys($body, ['data', 'links', 'meta']);
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
     *
     * @throws Exception
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
            'subfleet_id' => $subfleetA->id,
        ]);

        factory(App\Models\Aircraft::class, $subfleetB_size)->create([
            'subfleet_id' => $subfleetB->id,
        ]);

        $response = $this->get('/api/fleet');
        $response->assertStatus(200);
        $body = $response->json()['data'];

        foreach ($body as $subfleet) {
            if ($subfleet['id'] === $subfleetA->id) {
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
            'airline_id' => $this->user->airline_id,
        ]);

        $fare = factory(App\Models\Fare::class)->create();

        $fare_svc->setForSubfleet($subfleet, $fare);
        $aircraft = factory(App\Models\Aircraft::class)->create([
            'subfleet_id' => $subfleet->id,
        ]);

        /**
         * Just try retrieving by ID
         */
        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id);
        $body = $resp->json()['data'];
        $this->assertEquals($body['id'], $aircraft->id);

        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id.'?registration='.$aircraft->registration);
        $body = $resp->json()['data'];
        $this->assertEquals($body['id'], $aircraft->id);

        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id.'?icao='.$aircraft->icao);
        $body = $resp->json()['data'];
        $this->assertEquals($body['id'], $aircraft->id);
    }

    public function testGetAllSettings()
    {
        $this->user = factory(App\Models\User::class)->create();
        $res = $this->get('/api/settings')->assertStatus(200);
        $settings = $res->json();
    }
}
