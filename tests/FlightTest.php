<?php

use App\Services\FlightService;
use App\Models\Flight;
use App\Models\User;
use App\Models\UserBid;

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
        $flight = factory(App\Models\Flight::class)->create();
        $flight->subfleets()->syncWithoutDetaching([
            factory(App\Models\Subfleet::class)->create()->id
        ]);

        return $flight;
    }

    public function testGetFlight()
    {
        $flight = $this->addFlight();

        $req = $this->get('/api/flights/'.$flight->id, self::$auth_headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals($flight->id, $body['id']);
        $this->assertEquals($flight->dpt_airport_id, $body['dpt_airport_id']);
        $this->assertEquals($flight->arr_airport_id, $body['arr_airport_id']);

        $this->get('/api/flights/INVALID', self::$auth_headers)
            ->assertStatus(404);
    }

    /**
     * Search based on all different criteria
     */
    public function testSearchFlight()
    {
        $flight = $this->addFlight();

        # search specifically for a flight ID
        $query = 'flight_id=' . $flight->id;
        $req = $this->get('/api/flights/search?' . $query, self::$auth_headers);
        $req->assertStatus(200);
    }

    /**
     * Add/remove a bid, test the API, etc
     * @throws \App\Services\Exception
     */
    public function testBids()
    {
        $user = factory(User::class)->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight();

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        # Refresh
        $flight = Flight::find($flight->id);
        $this->assertTrue($flight->has_bid);

        # Check the table and make sure thee entry is there
        $user_bid = UserBid::where(['flight_id'=>$flight->id, 'user_id'=>$user->id])->get();
        $this->assertNotNull($user_bid);

        $user->refresh();
        $this->assertEquals(1, $user->bids->count());

        # Query the API and see that the user has the bids
        # And pull the flight details for the user/bids
        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals(1, sizeof($body['bids']));
        $this->assertEquals($flight->id, $body['bids'][0]['flight_id']);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);

        $body = $req->json();
        $req->assertStatus(200);
        $this->assertEquals($flight->id, $body[0]['id']);

        # Now remove the flight and check API

        $this->flightSvc->removeBid($flight, $user);

        $flight = Flight::find($flight->id);
        $this->assertFalse($flight->has_bid);

        $user->refresh();
        $bids = $user->bids()->get();
        $this->assertTrue($bids->isEmpty());

        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals($user->id, $body['id']);
        $this->assertEquals(0, sizeof($body['bids']));

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
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

        $user1 = factory(User::class)->create();;
        $user2 = factory(User::class)->create();

        $flight = $this->addFlight();

        # Put bid on the flight to block it off
        $bid = $this->flightSvc->addBid($flight, $user1);

        $bidRepeat = $this->flightSvc->addBid($flight, $user2);
        $this->assertNull($bidRepeat);
    }

    /**
     * Delete a flight and make sure all the bids are gone
     */
    public function testDeleteFlight()
    {
        $user = factory(User::class)->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight();

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        $this->flightSvc->deleteFlight($flight);

        $empty_flight = Flight::find($flight->id);
        $this->assertNull($empty_flight);

        # Make sure no bids exist
        $user_bids = UserBid::where('flight_id', $flight->id)->get();

        #$this->assertEquals(0, $user_bid->count());

        # Query the API and see that the user has the bids
        # And pull the flight details for the user/bids
        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);
        $body = $req->json();

        $this->assertEquals($user->id, $body['id']);
        $this->assertEquals(0, sizeof($body['bids']));

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
        $req->assertStatus(200);

        $body = $req->json();
        $this->assertEquals(0, sizeof($body));
    }
}
