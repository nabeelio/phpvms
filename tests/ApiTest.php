<?php

#use Swagger\Serializer;

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
        $airport = factory(App\Models\Airport::class)->create();

        $uri = '/api/airports/' . $airport->icao;

        // Missing auth header
        $this->get($uri)->assertStatus(401);

        // Test invalid API key
        $this->withHeaders(['Authorization' => 'invalidKey'])->get($uri)
            ->assertStatus(401);

        $this->withHeaders(['Authorization' => ''])->get($uri)
            ->assertStatus(401);

        // Test upper/lower case of Authorization header, etc
        $this->withHeaders($this->apiHeaders())->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => $airport->icao], true);

        $this->withHeaders(['authorization' => 'testadminapikey'])->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => $airport->icao], true);

        $this->withHeaders(['AUTHORIZATION' => 'testadminapikey'])->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => $airport->icao], true);

        $this->withHeaders(['AuThOrIzAtIoN' => 'testadminapikey'])->get($uri)
            ->assertStatus(200)
            ->assertJson(['icao' => $airport->icao], true);
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

        /*$body = $response->json();
        $serializer = new Serializer();
        $swagger = $serializer->deserialize(\json_encode($body));
        echo $swagger;*/

        $this->withHeaders($this->apiHeaders())->get('/api/airports/UNK')
            ->assertStatus(404);
    }
}
