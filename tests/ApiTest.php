<?php

/**
 * Test API calls and authentication, etc
 */
class ApiTest extends TestCase
{
    protected static $headers = [
        'Authorization' => 'testapikey'
    ];

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
        $uri = '/api/airports/kjfk';

        // Missing auth header
        $this->get($uri)->assertStatus(401);

        // Test invalid API key
        $this->withHeaders(['Authorization' => 'invalidKey'])->get($uri)
            ->assertStatus(401);

        // Test upper/lower case of Authorization header, etc
        $this->withHeaders(self::$headers)->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => 'KJFK'], true);

        $this->withHeaders(['AUTHORIZATION' => 'testapikey'])->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => 'KJFK'], true);
    }

    /**
     * Make sure the airport data is returned
     */
    public function testAirportRequest()
    {
        $this->withHeaders(self::$headers)->get('/api/airports/KJFK')
            ->assertStatus(200)
            ->assertJson(['icao' => 'KJFK'], true);

        $this->withHeaders(self::$headers)->get('/api/airports/UNK')
            ->assertStatus(404);
    }
}
