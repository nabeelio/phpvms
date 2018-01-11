<?php

use App\Services\UserService;
use Tests\TestData;

class PilotTest extends TestCase
{
    protected $userSvc;

    public function setUp()
    {
        parent::setUp();
        $this->userSvc = app(UserService::class);
    }

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
        $resp = $this->get('/api/user/fleet', [], $user);
        $body = $resp->json();

        # Get the subfleet that's been added in
        $subfleet_from_api = $body['data'][0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        # Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);

        /**
         * Check the user ID call
         */
        $resp = $this->get('/api/users/'.$user->id.'/fleet', [], $user);
        $body = $resp->json();

        # Get the subfleet that's been added in
        $subfleet_from_api = $body['data'][0];
        $this->assertEquals($subfleet->id, $subfleet_from_api['id']);

        # Get all the aircraft from that subfleet
        $aircraft_from_api = collect($subfleet_from_api['aircraft'])->pluck('id');
        $this->assertEquals($added_aircraft, $aircraft_from_api);
    }
}
