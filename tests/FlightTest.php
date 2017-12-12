<?php

use App\Services\FlightService;
use App\Models\Flight;
use App\Models\User;

class FlightTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->addData('base');

        $this->flightSvc = app(FlightService::class);
    }

    public function addFlight()
    {
        $flight = new App\Models\Flight;
        $flight->airline_id = 1;
        $flight->flight_number = 10;
        $flight->dpt_airport_id = 'KAUS';
        $flight->arr_airport_id = 'KJFK';
        $flight->save();

        # subfleet ID is in the base.yml
        $flight->subfleets()->syncWithoutDetaching([1]);

        return $flight->id;
    }

    public function testGetFlight()
    {
        $flight_id = $this->addFlight();
        $req = $this->get('/api/flights/'.$flight_id, self::$auth_headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals($flight_id, $body['id']);
        $this->assertEquals('KAUS', $body['dpt_airport_id']);
        $this->assertEquals('KJFK', $body['arr_airport_id']);

        $this->get('/api/flights/INVALID', self::$auth_headers)
            ->assertStatus(404);
    }

    /**
     * Search based on all different criteria
     */
    public function testSearchFlight()
    {
        $flight_id = $this->addFlight();

        # search specifically for a flight ID
        $query = 'flight_id='.$flight_id;
        $req = $this->get('/api/flights/search?' . $query, self::$auth_headers);
        $req->assertStatus(200);
    }

    /**
     * Add/remove a bid, test the API, etc
     * @throws \App\Services\Exception
     */
    public function testBids()
    {
        $flight_id = $this->addFlight();

        $user = User::find(1);
        $flight = Flight::find($flight_id);

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals(1, $bid->user_id);
        $this->assertEquals($flight_id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        # Refresh
        $flight = Flight::find($flight_id);
        $this->assertTrue($flight->has_bid);

        # Query the API and see that the user has the bids
        # And pull the flight details for the user/bids
        $req = $this->get('/api/user', self::$auth_headers);
        $req->assertStatus(200);
        $body = $req->json();
        $this->assertEquals(1, sizeof($body['bids']));
        $this->assertEquals($flight_id, $body['bids'][0]['flight_id']);

        $req = $this->get('/api/users/1/bids', self::$auth_headers);

        $body = $req->json();
        $req->assertStatus(200);
        $this->assertEquals($flight_id, $body[0]['id']);

        # Now remove the flight and check API

        $this->flightSvc->removeBid($flight, $user);

        $flight = Flight::find($flight_id);
        $this->assertFalse($flight->has_bid);

        $user = User::find(1);
        $bids = $user->bids()->get();
        $this->assertTrue($bids->isEmpty());

        $req = $this->get('/api/user', self::$auth_headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals(0, sizeof($body['bids']));

        $req = $this->get('/api/users/1/bids', self::$auth_headers);
        $req->assertStatus(200);
        $body = $req->json();

        $this->assertEquals(0, sizeof($body));
    }

    /**
     *
     */
    public function testMultipleBidsSingleFlight()
    {
        setting('bids.disable_flight_on_bid', true);

        $user1 = User::find(1);
        $user2 = User::find(2);

        $flight_id = $this->addFlight();
        $flight = Flight::find($flight_id);

        # Put bid on the flight to block it off
        $bid = $this->flightSvc->addBid($flight, $user1);

        $bidRepeat = $this->flightSvc->addBid($flight, $user2);
        $this->assertNull($bidRepeat);
    }
}
