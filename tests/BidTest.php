<?php

namespace Tests;

use App\Exceptions\BidExistsForFlight;
use App\Models\Bid;
use App\Models\Fare;
use App\Models\Flight;
use App\Models\Subfleet;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\BidService;
use App\Services\FareService;
use App\Services\FlightService;

class BidTest extends TestCase
{
    /** @var BidService */
    protected $bidSvc;

    /** @var FlightService */
    protected $flightSvc;

    /** @var SettingRepository */
    protected $settingsRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');

        $this->bidSvc = app(BidService::class);
        $this->flightSvc = app(FlightService::class);
        $this->settingsRepo = app(SettingRepository::class);
    }

    public function addFlight($user, $subfleet = null)
    {
        $flight = Flight::factory()->create([
            'airline_id' => $user->airline_id,
        ]);

        if ($subfleet === null) {
            /** @var Subfleet $subfleet */
            $subfleet = Subfleet::factory()->create([
                'airline_id' => $user->airline_id,
            ])->id;
        }

        $flight->subfleets()->syncWithoutDetaching([$subfleet]);

        return $flight;
    }

    /**
     * Add/remove a bid, test the API, etc
     *
     * @throws Exception|\Exception
     */
    public function testBids()
    {
        $this->settingsRepo->store('bids.allow_multiple_bids', true);
        $this->settingsRepo->store('bids.disable_flight_on_bid', false);

        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(2, [$subfleet['subfleet']->id]);

        /** @var FareService $fare_svc */
        $fare_svc = app(FareService::class);

        /** @var Fare $fare */
        $fare = Fare::factory()->create();
        $fare_svc->setForSubfleet($subfleet['subfleet'], $fare, [
            'price' => 50, 'capacity' => 400,
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'flight_time' => 1000,
            'rank_id'     => $rank->id,
        ]);

        $headers = $this->headers($user);

        /** @var Flight $flight */
        $flight = $this->addFlight($user, $subfleet['subfleet']->id);

        $bid = $this->bidSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        // Refresh
        $flight = Flight::find($flight->id);
        $this->assertTrue($flight->has_bid);

        // Check the table and make sure the entry is there
        $bid_retrieved = $this->bidSvc->addBid($flight, $user);
        $this->assertEquals($bid->id, $bid_retrieved->id);

        $user->refresh();
        $bids = $user->bids;
        $this->assertEquals(1, $bids->count());

        // Query the API and see that the user has the bids
        // And pull the flight details for the user/bids
        $req = $this->get('/api/user/bids', $headers);

        $body = $req->json()['data'];
        $req->assertStatus(200);
        $this->assertEquals($flight->id, $body[0]['flight_id']);

        // Make sure subfleets and fares are included
        $this->assertNotNull($body[0]['flight']['subfleets']);
        $this->assertNotNull($body[0]['flight']['subfleets'][0]['fares']);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);

        $body = $req->json()['data'];
        $req->assertStatus(200);
        $this->assertEquals($flight->id, $body[0]['flight_id']);

        // have a second user bid on it

        /** @var User $user */
        $user2 = User::factory()->create(['rank_id' => $rank->id]);

        $bid_user2 = $this->bidSvc->addBid($flight, $user2);
        $this->assertNotNull($bid_user2);
        $this->assertNotEquals($bid_retrieved->id, $bid_user2->id);

        // Now remove the flight and check API

        $this->bidSvc->removeBid($flight, $user);

        $flight = Flight::find($flight->id);

        // user2 still has a bid on it
        $this->assertTrue($flight->has_bid);

        // Remove it from 2nd user
        $this->bidSvc->removeBid($flight, $user2);
        $flight->refresh();
        $this->assertFalse($flight->has_bid);

        $user->refresh();
        $bids = $user->bids()->get();
        $this->assertTrue($bids->isEmpty());

        $req = $this->get('/api/user/bids', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertCount(0, $body);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
        $req->assertStatus(200);
        $body = $req->json()['data'];
        $this->assertCount(0, $body);
    }

    public function testMultipleBidsSingleFlight()
    {
        $this->settingsRepo->store('bids.disable_flight_on_bid', true);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create([
            'airline_id' => $user1->airline_id,
        ]);

        $flight = $this->addFlight($user1);

        // Put bid on the flight to block it off
        $this->bidSvc->addBid($flight, $user1);

        // Try adding again, should throw an exception
        $this->expectException(BidExistsForFlight::class);
        $this->bidSvc->addBid($flight, $user2);
    }

    /**
     * Add a flight bid VIA the API
     */
    public function testAddBidApi()
    {
        $this->user = User::factory()->create();
        $user2 = User::factory()->create();

        /** @var \App\Models\Flight $flight */
        $flight = $this->addFlight($this->user);

        $uri = '/api/user/bids';
        $data = ['flight_id' => $flight->id];

        $body = $this->put($uri, $data);
        $body = $body->json('data');

        $this->assertEquals($body['flight_id'], $flight->id);
        $this->assertNotEmpty($body['flight']);

        $res = $this->get('/api/bids/'.$body['id']);
        $res->assertOk();

        $body = $res->json('data');
        $this->assertEquals($body['flight_id'], $flight->id);
        $this->assertNotEmpty($body['flight']);

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
    public function testDeleteFlightWithBids()
    {
        $user = User::factory()->create();
        $headers = $this->headers($user);

        $flight = $this->addFlight($user);

        $bid = $this->bidSvc->addBid($flight, $user);
        $this->assertEquals($user->id, $bid->user_id);
        $this->assertEquals($flight->id, $bid->flight_id);
        $this->assertTrue($flight->has_bid);

        $this->flightSvc->deleteFlight($flight);

        $empty_flight = Flight::find($flight->id);
        $this->assertNull($empty_flight);

        // Make sure no bids exist
        $user_bids_count = Bid::where(['flight_id' => $flight->id])->count();
        $this->assertEquals(0, $user_bids_count);

        // Query the API and see that the user has the bids
        // And pull the flight details for the user/bids
        $req = $this->get('/api/user/bids', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertCount(0, $body);

        $req = $this->get('/api/users/'.$user->id.'/bids', $headers);
        $req->assertStatus(200);

        $body = $req->json()['data'];
        $this->assertCount(0, $body);
    }
}
