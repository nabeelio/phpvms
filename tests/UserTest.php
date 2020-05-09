<?php

use App\Exceptions\UserPilotIdExists;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    protected $settingsRepo;
    protected $userSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->userSvc = app(UserService::class);
        $this->settingsRepo = app(SettingRepository::class);
    }

    /**
     * Makes sure that the subfleet/aircraft returned are allowable
     * by the users rank.
     */
    public function testRankSubfleets()
    {
        // Add subfleets and aircraft, but also add another
        // set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft();
        $this->createSubfleetWithAircraft();

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);

        $user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $added_aircraft = $subfleetA['aircraft']->pluck('id');

        $subfleets = $this->userSvc->getAllowableSubfleets($user);
        $this->assertEquals(1, $subfleets->count());

        $subfleet = $subfleets[0];
        $all_aircraft = $subfleet->aircraft->pluck('id');
        $this->assertEquals($added_aircraft, $all_aircraft);

        /**
         * Check via API
         */
        $resp = $this->get('/api/user/fleet', [], $user)->assertStatus(200);
        $body = $resp->json()['data'];

        // Get the subfleet that's been added in
        $subfleet_from_api = $body[0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        // Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);

        /**
         * Check the user ID call
         */
        $resp = $this->get('/api/users/'.$user->id.'/fleet', [], $user)->assertStatus(200);
        $body = $resp->json()['data'];

        // Get the subfleet that's been added in
        $subfleet_from_api = $body[0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        // Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);
    }

    /**
     * Flip the setting for getting all of the user's aircraft restricted
     * by rank. Make sure that they're all returned
     */
    public function testGetAllAircraft()
    {
        // Add subfleets and aircraft, but also add another
        // set of subfleets
        $subfleetA = $this->createSubfleetWithAircraft();
        $subfleetB = $this->createSubfleetWithAircraft();

        $added_aircraft = array_merge(
            $subfleetA['aircraft']->pluck('id')->toArray(),
            $subfleetB['aircraft']->pluck('id')->toArray()
        );

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);

        $user = factory(App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);

        $subfleets = $this->userSvc->getAllowableSubfleets($user);
        $this->assertEquals(2, $subfleets->count());

        $all_aircraft = array_merge(
            $subfleets[0]->aircraft->pluck('id')->toArray(),
            $subfleets[1]->aircraft->pluck('id')->toArray()
        );

        $this->assertEquals($added_aircraft, $all_aircraft);

        /**
         * Check via API
         */
        $resp = $this->get('/api/user/fleet', [], $user)->assertStatus(200);

        // Get all the aircraft from that subfleet
        $body = $resp->json()['data'];
        $aircraft_from_api = array_merge(
            collect($body[0]['aircraft'])->pluck('id')->toArray(),
            collect($body[1]['aircraft'])->pluck('id')->toArray()
        );

        $this->assertEquals($added_aircraft, $aircraft_from_api);
    }

    /**
     * Flip the setting for getting all of the user's aircraft restricted
     * by rank. Make sure that they're all returned. Create two subfleets,
     * assign only one of them to the user's rank. When calling the api
     * to retrieve the flight, only subfleetA should be showing
     */
    public function testGetAircraftAllowedFromFlight()
    {
        // Add subfleets and aircraft, but also add another
        // set of subfleets
        $airport = factory(App\Models\Airport::class)->create();
        $subfleetA = $this->createSubfleetWithAircraft(2, $airport->id);
        $subfleetB = $this->createSubfleetWithAircraft(2);

        $rank = $this->createRank(10, [$subfleetA['subfleet']->id]);
        $user = factory(App\Models\User::class)->create([
            'curr_airport_id' => $airport->id,
            'rank_id'         => $rank->id,
        ]);

        $flight = factory(App\Models\Flight::class)->create([
            'airline_id'     => $user->airline_id,
            'dpt_airport_id' => $airport->id,
        ]);

        $flight->subfleets()->syncWithoutDetaching([
            $subfleetA['subfleet']->id,
            $subfleetB['subfleet']->id,
        ]);

        // Make sure no flights are filtered out
        $this->settingsRepo->store('pilots.only_flights_from_current', false);

        // And restrict the aircraft
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', false);

        $response = $this->get('/api/flights/'.$flight->id, [], $user);
        $response->assertStatus(200);
        $this->assertCount(2, $response->json()['data']['subfleets']);

        /*
         * Now make sure it's filtered out
         */
        $this->settingsRepo->store('pireps.restrict_aircraft_to_rank', true);

        /**
         * Make sure it's filtered out from the single flight call
         */
        $response = $this->get('/api/flights/'.$flight->id, [], $user);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']['subfleets']);

        /**
         * Make sure it's filtered out from the flight list
         */
        $response = $this->get('/api/flights', [], $user);
        $body = $response->json()['data'];
        $response->assertStatus(200);
        $this->assertCount(1, $body[0]['subfleets']);

        /**
         * Filtered from search?
         */
        $response = $this->get('/api/flights/search?flight_id='.$flight->id, [], $user);
        $response->assertStatus(200);
        $body = $response->json()['data'];
        $this->assertCount(1, $body[0]['subfleets']);
    }

    /**
     * Test the pilot ID being added when a new user is created
     */
    public function testUserPilotIdChangeAlreadyExists()
    {
        $this->expectException(UserPilotIdExists::class);
        $user1 = factory(App\Models\User::class)->create(['id' => 1]);
        $user2 = factory(App\Models\User::class)->create(['id' => 2]);

        // Now try to change the original user's pilot_id to 2 (should conflict)
        $this->userSvc->changePilotId($user1, 2);
    }

    /**
     * Test the pilot ID being added when a new user is created
     */
    public function testUserPilotIdAdded()
    {
        $new_user = factory(App\Models\User::class)->make()->toArray();
        $new_user['password'] = Hash::make('secret');
        $user = $this->userSvc->createUser($new_user);
        $this->assertEquals($user->id, $user->pilot_id);

        // Add a second user
        $new_user = factory(App\Models\User::class)->make()->toArray();
        $new_user['password'] = Hash::make('secret');
        $user2 = $this->userSvc->createUser($new_user);
        $this->assertEquals($user2->id, $user2->pilot_id);

        // Now try to change the original user's pilot_id to 3
        $user = $this->userSvc->changePilotId($user, 3);
        $this->assertEquals(3, $user->pilot_id);

        // Create a new user and the pilot_id should be 4
        $user3 = factory(App\Models\User::class)->create();
        $this->assertEquals(4, $user3->pilot_id);
    }

    /**
     * Test that a user's name is private
     */
    public function testUserNamePrivate()
    {
        $vals = [
            'Firstname'                     => 'Firstname',
            'Firstname Lastname'            => 'Firstname L',
            'Firstname Middlename Lastname' => 'Firstname L',
        ];

        foreach ($vals as $input => $expected) {
            $user = new User(['name' => $input]);
            $this->assertEquals($expected, $user->name_private);
        }
    }
}
