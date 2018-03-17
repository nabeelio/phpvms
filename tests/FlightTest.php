<?php

use App\Models\Flight;
use App\Models\User;
use App\Models\Bid;
use App\Repositories\SettingRepository;
use App\Services\FlightService;

class FlightTest extends TestCase
{
    protected $flightSvc, $settingsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->addData('base');

        $this->flightSvc = app(FlightService::class);
        $this->settingsRepo = app(SettingRepository::class);
    }

    public function addFlight($user)
    {
        $flight = factory(App\Models\Flight::class)->create([
            'airline_id' => $user->airline_id
        ]);

        $flight->subfleets()->syncWithoutDetaching([
            factory(App\Models\Subfleet::class)->create([
                'airline_id' => $user->airline_id
            ])->id
        ]);

        return $flight;
    }

    public function testGetFlight()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flight = $this->addFlight($this->user);

        $req = $this->get('/api/flights/' . $flight->id);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertEquals($flight->id, $body['id']);
        $this->assertEquals($flight->dpt_airport_id, $body['dpt_airport_id']);
        $this->assertEquals($flight->arr_airport_id, $body['arr_airport_id']);

        # Distance conversion
        $this->assertHasKeys($body['distance'], ['mi', 'nmi', 'km']);

        $this->get('/api/flights/INVALID', self::$auth_headers)
            ->assertStatus(404);
    }

    /**
     * Search based on all different criteria
     */
    public function testSearchFlight()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flight = $this->addFlight($this->user);

        # search specifically for a flight ID
        $query = 'flight_id=' . $flight->id;
        $req = $this->get('/api/flights/search?' . $query);
        $req->assertStatus(200);
    }

    /**
     * Get the flight's route
     */
    public function testFlightRoute()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flight = $this->addFlight($this->user);

        $route_count = random_int(4, 6);
        $route = factory(App\Models\Navdata::class, $route_count)->create();
        $route_text = implode(' ', $route->pluck('id')->toArray());

        $flight->route = $route_text;
        $flight->save();

        $res = $this->get('/api/flights/'.$flight->id.'/route');
        $res->assertStatus(200);
        $body = $res->json();

        $this->assertCount($route_count, $body['data']);

        $first_point = $body['data'][0];
        $this->assertEquals($first_point['id'], $route[0]->id);
        $this->assertEquals($first_point['name'], $route[0]->name);
        $this->assertEquals($first_point['type']['type'], $route[0]->type);
        $this->assertEquals(
            $first_point['type']['name'],
            \App\Models\Enums\NavaidType::label($route[0]->type)
        );
    }

    /**
     * Find all of the flights
     */
    public function testFindAllFlights()
    {
        $this->user = factory(App\Models\User::class)->create();
        factory(App\Models\Flight::class, 20)->create([
            'airline_id' => $this->user->airline_id
        ]);

        $res = $this->get('/api/flights');

        $body = $res->json();
        $this->assertEquals(2, $body['meta']['last_page']);

        $res = $this->get('/api/flights?page=2');
        $res->assertJsonCount(5, 'data');
    }

    public function testFlightSearchApi()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flights = factory(App\Models\Flight::class, 10)->create([
            'airline_id' => $this->user->airline_id
        ]);

        $flight = $flights->random();

        $query = 'flight_number=' . $flight->flight_number;
        $req = $this->get('/api/flights/search?' . $query);
        $body = $req->json();

        $this->assertEquals($flight->id, $body['data'][0]['id']);
    }

    /**
     * Add/remove a bid, test the API, etc
     * @throws \App\Services\Exception
     */
    public function testBids()
    {
        $user = factory(User::class)->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight($user);

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        # Refresh
        $flight = Flight::find($flight->id);
        $this->assertTrue($flight->has_bid);

        # Check the table and make sure thee entry is there
        $this->expectException(\App\Exceptions\BidExists::class);
        $this->flightSvc->addBid($flight, $user);

        $user->refresh();
        $this->assertEquals(1, $user->bids->count());

        # Query the API and see that the user has the bids
        # And pull the flight details for the user/bids
        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertEquals(1, sizeof($body['bids']));
        $this->assertEquals($flight->id, $body['bids'][0]['flight_id']);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);

        $body = $req->json()['data'];
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

        $body = $req->json()['data'];
        $this->assertEquals($user->id, $body['id']);
        $this->assertEquals(0, sizeof($body['bids']));

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
        $req->assertStatus(200);
        $body = $req->json()['data'];

        $this->assertCount(0, $body);
    }

    /**
     *
     */
    public function testMultipleBidsSingleFlight()
    {
        $this->settingsRepo->store('bids.disable_flight_on_bid', true);

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create([
            'airline_id' => $user1->airline_id
        ]);

        $flight = $this->addFlight($user1);

        # Put bid on the flight to block it off
        $this->flightSvc->addBid($flight, $user1);

        # Try adding again, should throw an exception
        $this->expectException(\App\Exceptions\BidExists::class);
        $this->flightSvc->addBid($flight, $user2);
    }

    /**
     * Add a flight bid VIA the API
     */
    public function testAddBidApi()
    {
        $this->user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $flight = $this->addFlight($this->user);

        $uri = '/api/user/bids';
        $data = ['flight_id' => $flight->id];

        $body = $this->put($uri, $data);
        $body = $body->json('data');

        $this->assertEquals($body['flight_id'], $flight->id);

        # Now try to have the second user bid on it
        # Should return a 409 error
        $response = $this->put($uri, $data, [], $user2);
        $response->assertStatus(409);

        # Try now deleting the bid from the user
        $response = $this->delete($uri, $data);
        $body = $response->json('data');
        $this->assertCount(0, $body);
    }

    /**
     * Delete a flight and make sure all the bids are gone
     */
    public function testDeleteFlight()
    {
        $user = factory(User::class)->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight($user);

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        $this->flightSvc->deleteFlight($flight);

        $empty_flight = Flight::find($flight->id);
        $this->assertNull($empty_flight);

        # Make sure no bids exist
        $user_bids = Bid::where('flight_id', $flight->id)->get();

        #$this->assertEquals(0, $user_bid->count());

        # Query the API and see that the user has the bids
        # And pull the flight details for the user/bids
        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertEquals($user->id, $body['id']);
        $this->assertCount(0, $body['bids']);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertCount(0, $body);
    }
}
