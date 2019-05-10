<?php

use App\Models\Bid;
use App\Models\Enums\Days;
use App\Models\Flight;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\FlightService;

class FlightTest extends TestCase
{
    protected $flightSvc;
    protected $settingsRepo;

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
            'airline_id' => $user->airline_id,
        ]);

        $flight->subfleets()->syncWithoutDetaching([
            factory(App\Models\Subfleet::class)->create([
                'airline_id' => $user->airline_id,
            ])->id,
        ]);

        return $flight;
    }

    /**
     * Test adding a flight and also if there are duplicates
     */
    public function testDuplicateFlight()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flight = $this->addFlight($this->user);

        // first flight shouldn't be a duplicate
        $this->assertFalse($this->flightSvc->isFlightDuplicate($flight));

        $flight_dupe = new Flight([
            'airline_id'    => $flight->airline_id,
            'flight_number' => $flight->flight_number,
            'route_code'    => $flight->route_code,
            'route_leg'     => $flight->route_leg,
        ]);

        $this->assertTrue($this->flightSvc->isFlightDuplicate($flight_dupe));

        // same flight but diff airline shouldn't be a dupe
        $new_airline = factory(App\Models\Airline::class)->create();
        $flight_dupe = new Flight([
            'airline_id'    => $new_airline->airline_id,
            'flight_number' => $flight->flight_number,
            'route_code'    => $flight->route_code,
            'route_leg'     => $flight->route_leg,
        ]);

        $this->assertFalse($this->flightSvc->isFlightDuplicate($flight_dupe));

        // add another flight with a code
        $flight_leg = factory(App\Models\Flight::class)->create([
            'airline_id'    => $flight->airline_id,
            'flight_number' => $flight->flight_number,
            'route_code'    => 'A',
        ]);

        $this->assertFalse($this->flightSvc->isFlightDuplicate($flight_leg));

        // Add both a route and leg
        $flight_leg = factory(App\Models\Flight::class)->create([
            'airline_id'    => $flight->airline_id,
            'flight_number' => $flight->flight_number,
            'route_code'    => 'A',
            'route_leg'     => 1,
        ]);

        $this->assertFalse($this->flightSvc->isFlightDuplicate($flight_leg));
    }

    public function testGetFlight()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flight = $this->addFlight($this->user);

        $req = $this->get('/api/flights/'.$flight->id);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertEquals($flight->id, $body['id']);
        $this->assertEquals($flight->dpt_airport_id, $body['dpt_airport_id']);
        $this->assertEquals($flight->arr_airport_id, $body['arr_airport_id']);

        // Distance conversion
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

        // search specifically for a flight ID
        $query = 'flight_id='.$flight->id;
        $req = $this->get('/api/flights/search?'.$query);
        $req->assertStatus(200);
    }

    /**
     * Get the flight's route
     *
     * @throws Exception
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
            'airline_id' => $this->user->airline_id,
        ]);

        $res = $this->get('/api/flights');

        $body = $res->json();
        $this->assertEquals(2, $body['meta']['last_page']);

        $res = $this->get('/api/flights?page=2');
        $res->assertJsonCount(5, 'data');
    }

    /**
     * Test the bitmasks that they work for setting the day of week and
     * then retrieving by searching on those
     */
    public function testFindDaysOfWeek(): void
    {
        $this->user = factory(App\Models\User::class)->create();
        factory(App\Models\Flight::class, 20)->create([
            'airline_id' => $this->user->airline_id,
        ]);

        $saved_flight = factory(App\Models\Flight::class)->create([
            'airline_id' => $this->user->airline_id,
            'days'       => Days::getDaysMask([
                Days::SUNDAY,
                Days::THURSDAY,
            ]),
        ]);

        $flight = Flight::findByDays([Days::SUNDAY])->first();
        $this->assertTrue($flight->on_day(Days::SUNDAY));
        $this->assertTrue($flight->on_day(Days::THURSDAY));
        $this->assertFalse($flight->on_day(Days::MONDAY));
        $this->assertEquals($saved_flight->id, $flight->id);

        $flight = Flight::findByDays([Days::SUNDAY, Days::THURSDAY])->first();
        $this->assertEquals($saved_flight->id, $flight->id);

        $flight = Flight::findByDays([Days::WEDNESDAY, Days::THURSDAY])->first();
        $this->assertNull($flight);
    }

    /**
     * Make sure that flights are marked as inactive when they're out of the start/end
     * zones. also make sure that flights with a specific day of the week are only
     * active on those days
     */
    public function testDayOfWeekActive(): void
    {
        $this->user = factory(App\Models\User::class)->create();

        // Set it to Monday or Tuesday, depending on what today is
        if (date('N') === '1') { // today is a monday
            $days = Days::getDaysMask([Days::TUESDAY]);
        } else {
            $days = Days::getDaysMask([Days::MONDAY]);
        }

        factory(App\Models\Flight::class, 5)->create();
        $flight = factory(App\Models\Flight::class)->create([
            'days' => $days,
        ]);

        // Run the event that will enable/disable flights
        $event = new \App\Events\CronNightly();
        (new \App\Cron\Nightly\SetActiveFlights())->handle($event);

        $res = $this->get('/api/flights');
        $body = $res->json('data');

        $flights = collect($body)->where('id', $flight->id)->first();
        $this->assertNull($flights);
    }

    public function testDayOfWeekTests(): void
    {
        $mask = 127;
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[1]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[2]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[3]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[4]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[5]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[6]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[7]));

        $mask = 125;
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[1]));
        $this->assertFalse(Days::in($mask, Days::$isoDayMap[2]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[3]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[4]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[5]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[6]));
        $this->assertTrue(Days::in($mask, Days::$isoDayMap[7]));
    }

    public function testStartEndDate(): void
    {
        $this->user = factory(App\Models\User::class)->create();

        factory(App\Models\Flight::class, 5)->create();
        $flight = factory(App\Models\Flight::class)->create([
            'start_date' => Carbon\Carbon::now('UTC')->subDays(1),
            'end_date'   => Carbon\Carbon::now('UTC')->addDays(1),
        ]);

        $flight_not_active = factory(App\Models\Flight::class)->create([
            'start_date' => Carbon\Carbon::now('UTC')->subDays(10),
            'end_date'   => Carbon\Carbon::now('UTC')->subDays(2),
        ]);

        // Run the event that will enable/disable flights
        $event = new \App\Events\CronNightly();
        (new \App\Cron\Nightly\SetActiveFlights())->handle($event);

        $res = $this->get('/api/flights');
        $body = $res->json('data');

        $flights = collect($body)->where('id', $flight->id)->first();
        $this->assertNotNull($flights);

        $flights = collect($body)->where('id', $flight_not_active->id)->first();
        $this->assertNull($flights);
    }

    public function testStartEndDateDayOfWeek(): void
    {
        $this->user = factory(App\Models\User::class)->create();

        // Set it to Monday or Tuesday, depending on what today is
        if (date('N') === '1') { // today is a monday
            $days = Days::getDaysMask([Days::TUESDAY]);
        } else {
            $days = Days::getDaysMask([Days::MONDAY]);
        }

        factory(App\Models\Flight::class, 5)->create();
        $flight = factory(App\Models\Flight::class)->create([
            'start_date' => Carbon\Carbon::now('UTC')->subDays(1),
            'end_date'   => Carbon\Carbon::now('UTC')->addDays(1),
            'days'       => Days::$isoDayMap[date('N')],
        ]);

        // Not active because of days of week not today
        $flight_not_active = factory(App\Models\Flight::class)->create([
            'start_date' => Carbon\Carbon::now('UTC')->subDays(1),
            'end_date'   => Carbon\Carbon::now('UTC')->addDays(1),
            'days'       => $days,
        ]);

        // Run the event that will enable/disable flights
        $event = new \App\Events\CronNightly();
        (new \App\Cron\Nightly\SetActiveFlights())->handle($event);

        $res = $this->get('/api/flights');
        $body = $res->json('data');

        $flights = collect($body)->where('id', $flight->id)->first();
        $this->assertNotNull($flights);

        $flights = collect($body)->where('id', $flight_not_active->id)->first();
        $this->assertNull($flights);
    }

    public function testFlightSearchApi()
    {
        $this->user = factory(App\Models\User::class)->create();
        $flights = factory(App\Models\Flight::class, 10)->create([
            'airline_id' => $this->user->airline_id,
        ]);

        $flight = $flights->random();

        $query = 'flight_number='.$flight->flight_number;
        $req = $this->get('/api/flights/search?'.$query);
        $body = $req->json();

        $this->assertEquals($flight->id, $body['data'][0]['id']);
    }

    public function testFlightSearchApiDistance()
    {
        $total_flights = 10;
        $this->user = factory(App\Models\User::class)->create();
        $flights = factory(App\Models\Flight::class, $total_flights)->create([
            'airline_id' => $this->user->airline_id,
        ]);

        // Max distance generated in factory is 1000, so set a random flight
        // and try to find it again through the search

        $flight = $flights->random();
        $flight->distance = 1500;
        $flight->save();

        $distance_gt = 1000;
        $distance_lt = 1600;

        // look for all of the flights now less than the "factory default" of 1000
        $query = 'dlt=1000&ignore_restrictions=1';
        $req = $this->get('/api/flights/search?'.$query);
        $body = $req->json();
        $this->assertCount($total_flights - 1, $body['data']);

        // Try using greater than
        $query = 'dgt='.$distance_gt.'&ignore_restrictions=1';
        $req = $this->get('/api/flights/search?'.$query);
        $body = $req->json();
        $this->assertCount(1, $body['data']);
        $this->assertEquals($flight->id, $body['data'][0]['id']);

        $query = 'dgt='.$distance_gt.'&dlt='.$distance_lt.'&ignore_restrictions=1';
        $req = $this->get('/api/flights/search?'.$query);
        $body = $req->json();
        $this->assertCount(1, $body['data']);
        $this->assertEquals($flight->id, $body['data'][0]['id']);
    }

    public function testAddSubfleet()
    {
        $subfleet = factory(App\Models\Subfleet::class)->create();
        $flight = factory(App\Models\Flight::class)->create();

        $fleetSvc = app(App\Services\FleetService::class);
        $fleetSvc->addSubfleetToFlight($subfleet, $flight);

        $flight->refresh();
        $found = $flight->subfleets()->get();
        $this->assertCount(1, $found);

        // Make sure it hasn't been added twice
        $fleetSvc->addSubfleetToFlight($subfleet, $flight);
        $flight->refresh();
        $found = $flight->subfleets()->get();
        $this->assertCount(1, $found);
    }

    /**
     * Add/remove a bid, test the API, etc
     *
     * @throws \App\Services\Exception
     */
    public function testBids()
    {
        $this->settingsRepo->store('bids.allow_multiple_bids', true);
        $this->settingsRepo->store('bids.disable_flight_on_bid', false);

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight($user);

        $bid = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        // Refresh
        $flight = Flight::find($flight->id);
        $this->assertTrue($flight->has_bid);

        // Check the table and make sure the entry is there
        $bid_retrieved = $this->flightSvc->addBid($flight, $user);
        $this->assertEquals($bid->id, $bid_retrieved->id);

        $user->refresh();
        $bids = $user->bids;
        $this->assertEquals(1, $bids->count());

        // Query the API and see that the user has the bids
        // And pull the flight details for the user/bids
        $req = $this->get('/api/user', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertCount(1, $body['bids']);
        $this->assertEquals($flight->id, $body['bids'][0]['flight_id']);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);

        $body = $req->json()['data'];
        $req->assertStatus(200);
        $this->assertEquals($flight->id, $body[0]['flight_id']);

        // have a second user bid on it
        $bid_user2 = $this->flightSvc->addBid($flight, $user2);
        $this->assertNotNull($bid_user2);
        $this->assertNotEquals($bid_retrieved->id, $bid_user2->id);

        // Now remove the flight and check API

        $this->flightSvc->removeBid($flight, $user);

        $flight = Flight::find($flight->id);

        // user2 still has a bid on it
        $this->assertTrue($flight->has_bid);

        // Remove it from 2nd user
        $this->flightSvc->removeBid($flight, $user2);
        $flight->refresh();
        $this->assertFalse($flight->has_bid);

        $user->refresh();
        $bids = $user->bids()->get();
        $this->assertTrue($bids->isEmpty());

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

    public function testMultipleBidsSingleFlight()
    {
        $this->settingsRepo->store('bids.disable_flight_on_bid', true);

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create([
            'airline_id' => $user1->airline_id,
        ]);

        $flight = $this->addFlight($user1);

        // Put bid on the flight to block it off
        $this->flightSvc->addBid($flight, $user1);

        // Try adding again, should throw an exception
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

        // Now try to have the second user bid on it
        // Should return a 409 error
        $response = $this->put($uri, $data, [], $user2);
        $response->assertStatus(409);

        // Try now deleting the bid from the user
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

        // Make sure no bids exist
        $user_bids = Bid::where('flight_id', $flight->id)->get();

        //$this->assertEquals(0, $user_bid->count());

        // Query the API and see that the user has the bids
        // And pull the flight details for the user/bids
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
