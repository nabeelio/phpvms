<?php

use App\Models\UserAward;

class AwardsTest extends TestCase
{
    private $awardSvc,
            $pirepSvc;

    public function setUp()
    {
        parent::setUp();
        $this->awardSvc = app(\App\Services\AwardService::class);
        $this->pirepSvc = app(\App\Services\PirepService::class);
    }

    /**
     * Make sure the awards classes are returned
     */
    public function testGetAwardsClasses()
    {
        $classes = $this->awardSvc->findAllAwardClasses();
        $this->assertCount(2, $classes);
    }

    /**
     * Test to make sure that the award is actually given out
     */
    public function testAwardsGiven()
    {
        // Create one award that's given out with one flight
        $award = factory(App\Models\Award::class)->create([
            'ref_model' => App\Awards\PilotFlightAwards::class,
            'ref_model_params' => 1,
        ]);

        $user = factory(App\Models\User::class)->create([
            'flights' => 0,
        ]);

        $pirep = factory(App\Models\Pirep::class)->create([
            'airline_id' => $user->airline->id,
            'user_id' => $user->id,
        ]);

        $this->pirepSvc->create($pirep);
        $this->pirepSvc->accept($pirep);

        $w = [
            'user_id' => $user->id,
            'award_id' => $award->id,
        ];

        # Make sure only one is awarded
        $this->assertEquals(1, UserAward::where($w)->count(['id']));

        $found_award = UserAward::where($w)->first();
        $this->assertNotNull($found_award);
    }
}
