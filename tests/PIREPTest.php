<?php

use App\Models\Acars;
use App\Models\Bid;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\FlightService;
use App\Services\PirepService;
use Carbon\Carbon;

class PIREPTest extends TestCase
{
    protected $pirepSvc;
    protected $settingsRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');
        $this->addData('fleet');

        $this->pirepSvc = app(PirepService::class);
        $this->settingsRepo = app(SettingRepository::class);
    }

    protected function createNewRoute()
    {
        $route = [];
        $navpoints = factory(App\Models\Navdata::class, 5)->create();
        foreach ($navpoints as $point) {
            $route[] = $point->id;
        }

        return $route;
    }

    protected function getAcarsRoute($pirep)
    {
        $saved_route = [];
        $route_points = Acars::where(
            ['pirep_id' => $pirep->id, 'type' => AcarsType::ROUTE]
        )->orderBy('order', 'asc')->get();

        foreach ($route_points as $point) {
            $saved_route[] = $point->name;
        }

        return $saved_route;
    }

    /**
     * @throws Exception
     */
    public function testAddPirep()
    {
        $user = factory(App\Models\User::class)->create();
        $route = $this->createNewRoute();
        $pirep = factory(App\Models\Pirep::class)->create([
            'user_id' => $user->id,
            'route'   => implode(' ', $route),
        ]);

        $pirep = $this->pirepSvc->create($pirep, []);

        try {
            $this->pirepSvc->saveRoute($pirep);
        } catch (Exception $e) {
            throw $e;
        }

        /*
         * Check the initial state info
         */
        $this->assertEquals($pirep->state, PirepState::PENDING);

        /**
         * Now set the PIREP state to ACCEPTED
         */
        $new_pirep_count = $pirep->pilot->flights + 1;
        $original_flight_time = $pirep->pilot->flight_time;
        $new_flight_time = $pirep->pilot->flight_time + $pirep->flight_time;

        $this->pirepSvc->changeState($pirep, PirepState::ACCEPTED);
        $this->assertEquals($new_pirep_count, $pirep->pilot->flights);
        $this->assertEquals($new_flight_time, $pirep->pilot->flight_time);
        $this->assertEquals($pirep->arr_airport_id, $pirep->pilot->curr_airport_id);

        // Check the location of the current aircraft
        $this->assertEquals($pirep->aircraft->airport_id, $pirep->arr_airport_id);

        // Also check via API:
        $this->get('/api/fleet/aircraft/'.$pirep->aircraft_id, [], $user)
             ->assertJson(['data' => ['airport_id' => $pirep->arr_airport_id]]);

        /**
         * Now go from ACCEPTED to REJECTED
         */
        $new_pirep_count = $pirep->pilot->flights - 1;
        $new_flight_time = $pirep->pilot->flight_time - $pirep->flight_time;
        $this->pirepSvc->changeState($pirep, PirepState::REJECTED);
        $this->assertEquals($new_pirep_count, $pirep->pilot->flights);
        $this->assertEquals($new_flight_time, $pirep->pilot->flight_time);
        $this->assertEquals($pirep->arr_airport_id, $pirep->pilot->curr_airport_id);

        /**
         * Check the ACARS table
         */
        $saved_route = $this->getAcarsRoute($pirep);
        $this->assertEquals($route, $saved_route);

        /**
         * Recreate the route with new options points. Make sure that the
         * old route is erased from the ACARS table and then recreated
         */
        $route = $this->createNewRoute();
        $pirep->route = implode(' ', $route);
        $pirep->save();

        // this should delete the old route from the acars table
        $this->pirepSvc->saveRoute($pirep);

        $saved_route = $this->getAcarsRoute($pirep);
        $this->assertEquals($route, $saved_route);
    }

    /**
     * Make sure the unit conversions look to be proper
     */
    public function testUnitFields()
    {
        $pirep = $this->createPirep();
        $pirep->save();

        $uri = '/api/pireps/'.$pirep->id;

        $response = $this->get($uri);
        $body = $response->json('data');

        // Check that it has the fuel units
        $this->assertHasKeys($body['fuel_used'], ['lbs', 'kg']);
        $this->assertEquals($pirep->fuel_used, $body['fuel_used']['lbs']);

        // Check that it has the distance units
        $this->assertHasKeys($body['distance'], ['km', 'nmi', 'mi']);
        $this->assertEquals($pirep->distance, $body['distance']['nmi']);

        // Check the planned_distance field
        $this->assertHasKeys($body['planned_distance'], ['km', 'nmi', 'mi']);
        $this->assertEquals($pirep->planned_distance, $body['planned_distance']['nmi']);
    }

    public function testGetUserPireps()
    {
        $this->user = factory(App\Models\User::class)->create();
        $pirep_done = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'state'   => PirepState::ACCEPTED,
        ]);

        $pirep_in_progress = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'state'   => PirepState::IN_PROGRESS,
        ]);

        $pirep_cancelled = factory(App\Models\Pirep::class)->create([
            'user_id' => $this->user->id,
            'state'   => PirepState::CANCELLED,
        ]);

        $pireps = $this->get('/api/user/pireps')
                    ->assertStatus(200)
                    ->json();

        $pirep_ids = collect($pireps['data'])->pluck('id');

        $this->assertTrue($pirep_ids->contains($pirep_done->id));
        $this->assertTrue($pirep_ids->contains($pirep_in_progress->id));
        $this->assertFalse($pirep_ids->contains($pirep_cancelled->id));

        // Get only status
        $pireps = $this->get('/api/user/pireps?state='.PirepState::IN_PROGRESS)
            ->assertStatus(200)
            ->json();

        $pirep_ids = collect($pireps['data'])->pluck('id');
        $this->assertTrue($pirep_ids->contains($pirep_in_progress->id));
        $this->assertFalse($pirep_ids->contains($pirep_done->id));
        $this->assertFalse($pirep_ids->contains($pirep_cancelled->id));
    }

    /**
     * check the stats/ranks, etc have incremented properly
     */
    public function testPilotStatsIncr()
    {
        $user = factory(User::class)->create([
            'flights'     => 0,
            'flight_time' => 0,
            'rank_id'     => 1,
        ]);

        // Submit two PIREPs
        $pireps = factory(Pirep::class, 2)->create([
            'airline_id'  => $user->airline_id,
            'aircraft_id' => 1,
            'user_id'     => $user->id,
            // 360min == 6 hours, rank should bump up
            'flight_time' => 360,
        ]);

        foreach ($pireps as $pirep) {
            $this->pirepSvc->create($pirep);
            $this->pirepSvc->accept($pirep);
        }

        $pilot = User::find($user->id);
        $last_pirep = Pirep::where('id', $pilot->last_pirep_id)->first();

        // Make sure rank went up
        $this->assertGreaterThan($user->rank_id, $pilot->rank_id);
        $this->assertEquals($last_pirep->arr_airport_id, $pilot->curr_airport_id);

        $this->assertEquals(2, $pilot->flights);

        //
        // Submit another PIREP, adding another 6 hours
        // it should automatically be accepted
        //
        $pirep = factory(Pirep::class)->create([
            'airline_id' => 1,
            'user_id'    => $user->id,
            // 120min == 2 hours, currently at 9 hours
            // Rank bumps up at 10 hours
            'flight_time' => 120,
        ]);

        // Pilot should be at rank 2, where accept should be automatic
        $this->pirepSvc->create($pirep);
        $this->pirepSvc->submit($pirep);

        $pilot->refresh();

        $this->assertEquals(3, $pilot->flights);

        $latest_pirep = Pirep::where('id', $pilot->last_pirep_id)->first();

        // Make sure PIREP was auto updated
        $this->assertEquals(PirepState::ACCEPTED, $latest_pirep->state);

        // Make sure latest PIREP was updated
        $this->assertNotEquals($last_pirep->id, $latest_pirep->id);
    }

    /**
     * Find and check for any duplicate PIREPs by a user
     */
    public function testDuplicatePireps()
    {
        $user = factory(App\Models\User::class)->create();
        $pirep = factory(Pirep::class)->create([
            'user_id' => $user->id,
        ]);

        // This should find itself...
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        $this->assertNotFalse($dupe_pirep);
        $this->assertEquals($pirep->id, $dupe_pirep->id);

        /**
         * Create a PIREP outside of the check time interval
         */
        $minutes = setting('pireps.duplicate_check_time') + 1;
        $pirep = factory(Pirep::class)->create([
            'created_at' => Carbon::now()->subMinutes($minutes)->toDateTimeString(),
        ]);

        // This should find itself...
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        $this->assertFalse($dupe_pirep);
    }

    public function testCancelViaAPI()
    {
        $pirep = $this->createPirep()->toArray();

        $uri = '/api/pireps/prefile';
        $response = $this->post($uri, $pirep);
        $pirep_id = $response->json()['data']['id'];

        $uri = '/api/pireps/'.$pirep_id.'/acars/position';
        $acars = factory(App\Models\Acars::class)->make()->toArray();
        $response = $this->post($uri, [
            'positions' => [$acars],
        ]);

        $response->assertStatus(200);

        // Cancel it
        $uri = '/api/pireps/'.$pirep_id.'/cancel';
        $response = $this->delete($uri, $acars);
        $response->assertStatus(200);

        // Should get a 400 when posting an ACARS update
        $uri = '/api/pireps/'.$pirep_id.'/acars/position';
        $acars = factory(App\Models\Acars::class)->make()->toArray();

        $response = $this->post($uri, $acars);
        $response->assertStatus(400);
    }

    /**
     * When a PIREP is accepted, ensure that the bid is removed
     */
    public function testPirepBidRemoved()
    {
        $flightSvc = app(FlightService::class);
        $this->settingsRepo->store('pireps.remove_bid_on_accept', true);

        $user = factory(App\Models\User::class)->create([
            'flight_time' => 0,
        ]);

        $flight = factory(App\Models\Flight::class)->create([
            'route_code' => null,
            'route_leg'  => null,
        ]);

        $flightSvc->addBid($flight, $user);

        $pirep = factory(App\Models\Pirep::class)->create([
            'user_id'       => $user->id,
            'airline_id'    => $flight->airline_id,
            'flight_number' => $flight->flight_number,
        ]);

        $pirep = $this->pirepSvc->create($pirep, []);
        $this->pirepSvc->changeState($pirep, PirepState::ACCEPTED);

        $user_bid = Bid::where([
            'user_id'   => $user->id,
            'flight_id' => $flight->id,
        ])->first();

        $this->assertNull($user_bid);
    }
}
