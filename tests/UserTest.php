<?php

use App\Services\UserService;
use App\Repositories\SettingRepository;

use Tests\TestData;

class UserTest extends TestCase
{
    protected $settingsRepo, $userSvc;

    public function setUp()
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
        # Add subfleets and aircraft, but also add another
        # set of subfleets
        $subfleetA = TestData::createSubfleetWithAircraft();
        TestData::createSubfleetWithAircraft();

        $rank = TestData::createRank(10, [$subfleetA['subfleet']->id]);

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
        $body = $resp->json();

        # Get the subfleet that's been added in
        $subfleet_from_api = $body[0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        # Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);

        /**
         * Check the user ID call
         */
        $resp = $this->get('/api/users/' . $user->id . '/fleet', [], $user)->assertStatus(200);
        $body = $resp->json();

        # Get the subfleet that's been added in
        $subfleet_from_api = $body[0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        # Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);
    }


    /**
     * Flip the setting for getting all of the user's aircraft restricted
     * by rank. Make sure that they're all returned
     */
    public function testGetAllAircraft()
    {
        # Add subfleets and aircraft, but also add another
        # set of subfleets
        $subfleetA = TestData::createSubfleetWithAircraft();
        $subfleetB = TestData::createSubfleetWithAircraft();

        $added_aircraft = array_merge(
            $subfleetA['aircraft']->pluck('id')->toArray(),
            $subfleetB['aircraft']->pluck('id')->toArray()
        );

        $rank = TestData::createRank(10, [$subfleetA['subfleet']->id]);

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
        $body = $resp->json();

        # Get all the aircraft from that subfleet
        $aircraft_from_api = array_merge(
            collect($body[0]['aircraft'])->pluck('id')->toArray(),
            collect($body[1]['aircraft'])->pluck('id')->toArray()
        );

        $this->assertEquals($added_aircraft, $aircraft_from_api);
    }
}
