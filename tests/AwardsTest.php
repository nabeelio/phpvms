<?php

namespace Tests;

use App\Models\Award;
use App\Models\Pirep;
use App\Models\User;
use App\Models\UserAward;
use App\Services\AwardService;
use App\Services\PirepService;
use Modules\Awards\Awards\PilotFlightAwards;

class AwardsTest extends TestCase
{
    /** @var AwardService */
    private $awardSvc;

    private $pirepSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');
        $this->addData('fleet');
        $this->awardSvc = app(AwardService::class);
        $this->pirepSvc = app(PirepService::class);
    }

    /**
     * Make sure the awards classes are returned
     */
    public function testGetAwardsClasses()
    {
        $classes = $this->awardSvc->findAllAwardClasses();
        $this->assertGreaterThanOrEqual(2, $classes);
    }

    /**
     * Test to make sure that the award is actually given out
     */
    public function testAwardsGiven()
    {
        // Create one award that's given out with one flight
        $award = factory(Award::class)->create([
            'ref_model'        => PilotFlightAwards::class,
            'ref_model_params' => 1,
        ]);

        $user = factory(User::class)->create([
            'flights' => 0,
        ]);

        $pirep = factory(Pirep::class)->create([
            'airline_id' => $user->airline->id,
            'user_id'    => $user->id,
        ]);

        $this->pirepSvc->create($pirep);
        $this->pirepSvc->accept($pirep);

        $w = [
            'user_id'  => $user->id,
            'award_id' => $award->id,
        ];

        // Make sure only one is awarded
        $this->assertEquals(1, UserAward::where($w)->count(['id']));

        $found_award = UserAward::where($w)->first();
        $this->assertNotNull($found_award);
    }
}
