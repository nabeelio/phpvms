<?php

namespace Tests;

//use Swagger\Serializer;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\UserState;
use App\Models\Fare;
use App\Models\News;
use App\Models\Subfleet;
use App\Models\User;
use App\Services\FareService;
use App\Support\Utils;
use Exception;
use function random_int;

/**
 * Test API calls and authentication, etc
 */
class ApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');
    }

    /**
     * Ensure authentication against the API works
     */
    public function testApiAuthentication()
    {
        $user = User::factory()->create();

        $uri = '/api/user';

        // Missing auth header
        $res = $this->get($uri);
        $res->assertStatus(401);

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
        $this->user = User::factory()->create([
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
        News::factory()->create();
        $response = $this->get('/api/news')->json();

        $this->assertCount(1, $response['data']);
        $this->assertArrayHasKey('user', $response['data'][0]);
    }

    /**
     * @throws Exception
     */
    public function testGetAirlines()
    {
        $size = random_int(5, 10);
        $this->user = User::factory()->create([
            'airline_id' => 0,
        ]);

        $airlines = Airline::factory()->count($size)->create();

        $res = $this->get('/api/airlines');
        $this->assertTrue($res->isOk());

        $airline = $airlines->random();
        $this->get('/api/airlines/'.$airline->id)
             ->assertJson(['data' => ['name' => $airline->name]]);
    }

    public function testGetAirlinesChineseChars()
    {
        $this->user = User::factory()->create([
            'airline_id' => 0,
        ]);

        Airline::factory()->create([
            'icao' => 'DKH',
            'name' => '吉祥航空',
        ]);

        Airline::factory()->create([
            'icao' => 'CSZ',
            'name' => '深圳航空',
        ]);

        Airline::factory()->create([
            'icao' => 'CCA',
            'name' => '中国国际航空',
        ]);

        Airline::factory()->create([
            'icao' => 'CXA',
            'name' => '厦门航空',
        ]);

        $res = $this->get('/api/airlines');
        $this->assertTrue($res->isOk());
    }

    /**
     * @throws Exception
     */
    public function testPagination()
    {
        $size = random_int(5, 10);
        $this->user = User::factory()->create([
            'airline_id' => 0,
        ]);

        Subfleet::factory()->count($size)->create();

        /*
         * Page 0 and page 1 should return the same thing
         */

        // Test pagination
        $res = $this->get('/api/fleet?limit=1&page=0');
        $this->assertTrue($res->isOk());
        $body = $res->json('data');

        $this->assertCount(1, $body);

        $id_first = $body[0]['id'];

        $res = $this->get('/api/fleet?limit=1&page=1');
        $this->assertTrue($res->isOk());
        $body = $res->json('data');

        $id_second = $body[0]['id'];

        $this->assertEquals($id_first, $id_second);

        /*
         * Page 2 should be different from page 1
         */

        $res = $this->get('/api/fleet?limit=1&page=2');
        $this->assertTrue($res->isOk());
        $body = $res->json('data');

        $id_third = $body[0]['id'];

        $this->assertNotEquals($id_first, $id_third);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest()
    {
        $this->user = User::factory()->create();
        $airport = Airport::factory()->create();

        $response = $this->get('/api/airports/'.$airport->icao);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['icao' => $airport->icao]]);

        $this->get('/api/airports/UNK')->assertStatus(404);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest5Char()
    {
        $this->user = User::factory()->create();

        /** @var Airport $airport */
        $airport = Airport::factory()->create(['icao' => '5Char']);

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
        $this->user = User::factory()->create();
        Airport::factory()->count(70)->create();

        $response = $this->get('/api/airports/')
                         ->assertStatus(200);

        $body = $response->json();
        $this->assertHasKeys($body, ['data', 'links', 'meta']);
    }

    public function testGetAllAirportsHubs()
    {
        $this->user = User::factory()->create();
        Airport::factory()->count(10)->create();
        Airport::factory()->create(['hub' => 1]);

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
        $this->user = User::factory()->create();

        $subfleetA = Subfleet::factory()->create([
            'airline_id' => $this->user->airline_id,
        ]);

        $subfleetB = Subfleet::factory()->create([
            'airline_id' => $this->user->airline_id,
        ]);

        $subfleetA_size = random_int(2, 10);
        $subfleetB_size = random_int(2, 10);
        Aircraft::factory()->count($subfleetA_size)->create([
            'subfleet_id' => $subfleetA->id,
        ]);

        Aircraft::factory()->count($subfleetB_size)->create([
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
            foreach ($subfleet['aircraft'] as $aircraft) {
                $this->assertNotEmpty($aircraft['ident']);
            }
        }
    }

    /**
     * Test getting an aircraft
     */
    public function testGetAircraft()
    {
        $this->user = User::factory()->create();

        $fare_svc = app(FareService::class);

        /** @var Subfleet $subfleet */
        $subfleet = Subfleet::factory()->create([
            'airline_id' => $this->user->airline_id,
        ]);

        /** @var Fare $fare */
        $fare = Fare::factory()->create();

        $fare_svc->setForSubfleet($subfleet, $fare);

        /** @var Aircraft $aircraft */
        $aircraft = Aircraft::factory()->create([
            'subfleet_id' => $subfleet->id,
        ]);

        /**
         * Just try retrieving by ID
         */
        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id);
        $body = $resp->json()['data'];

        $this->assertEquals($body['id'], $aircraft->id);
        $this->assertEquals($body['name'], $aircraft->name);
        $this->assertNotEmpty($body['ident']);
        $this->assertEquals($body['mtow'], $aircraft->mtow);
        $this->assertEquals($body['zfw'], $aircraft->zfw);

        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id.'?registration='.$aircraft->registration);
        $body = $resp->json()['data'];

        $this->assertEquals($body['id'], $aircraft->id);
        $this->assertEquals($body['name'], $aircraft->name);
        $this->assertEquals($body['mtow'], $aircraft->mtow);
        $this->assertEquals($body['zfw'], $aircraft->zfw);

        $this->assertNotEmpty($body['ident']);
        $this->assertEquals($body['ident'], $aircraft->ident);

        $resp = $this->get('/api/fleet/aircraft/'.$aircraft->id.'?icao='.$aircraft->icao);
        $body = $resp->json()['data'];

        $this->assertEquals($body['id'], $aircraft->id);
        $this->assertEquals($body['name'], $aircraft->name);
        $this->assertEquals($body['mtow'], $aircraft->mtow);
        $this->assertEquals($body['zfw'], $aircraft->zfw);
    }

    public function testGetAllSettings()
    {
        $this->user = User::factory()->create();
        $res = $this->get('/api/settings')->assertStatus(200);
        $settings = $res->json();
    }

    public function testGetUser()
    {
        $this->user = User::factory()->create([
            'avatar' => '/assets/avatar.jpg',
        ]);

        $res = $this->get('/api/user')->assertStatus(200);
        $user = $res->json('data');
        $this->assertNotNull($user);
        $this->assertTrue(strpos($user['avatar'], 'http') !== -1);

        // Should go to gravatar

        $this->user = User::factory()->create();

        $res = $this->get('/api/user')->assertStatus(200);
        $user = $res->json('data');
        $this->assertNotNull($user);
        $this->assertTrue(strpos($user['avatar'], 'gravatar') !== -1);
    }

    /**
     * Test that the web cron runs
     */
    public function testWebCron()
    {
        $this->updateSetting('cron.random_id', '');
        $this->get('/api/cron/sdf')->assertStatus(400);

        $id = Utils::generateNewId(24);
        $this->updateSetting('cron.random_id', $id);

        $this->get('/api/cron/sdf')->assertStatus(400);

        $res = $this->get('/api/cron/'.$id);
        $res->assertStatus(200);
    }

    public function testStatus()
    {
        $res = $this->get('/api/status');
        $status = $res->json();

        $this->assertNotEmpty($status['version']);
        $this->assertNotEmpty($status['php']);
    }
}
