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
        $user = factory(User::class)->create([
            'state' => UserState::PENDING
        ]);

        $uri = '/api/user';
        $this->withHeaders(['x-api-key' => $user->api_key])->get($uri)
            ->assertStatus(401);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest()
    {
        $airport = factory(App\Models\Airport::class)->create();

        $response = $this->withHeaders($this->apiHeaders())->get('/api/airports/' . $airport->icao);
        $response->assertStatus(200);
        $response->assertJson(['icao' => $airport->icao], true);

        $this->withHeaders($this->apiHeaders())->get('/api/airports/UNK')
            ->assertStatus(404);
    }
}
