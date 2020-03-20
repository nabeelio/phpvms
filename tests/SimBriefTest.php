<?php

use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\SimBrief;
use App\Services\SimBriefService;
use App\Support\Utils;
use Carbon\Carbon;

class SimBriefTest extends TestCase
{
    /**
     * Load SimBrief
     *
     * @param \App\Models\User $user
     *
     * @return \App\Models\SimBrief
     */
    protected function loadSimBrief($user): SimBrief
    {
        $flight = factory(App\Models\Flight::class)->create([
            'dpt_airport_id' => 'OMAA',
            'arr_airport_id' => 'OMDB',
        ]);

        $this->mockXmlResponse('briefing.xml');

        /** @var SimBriefService $sb */
        $sb = app(SimBriefService::class);

        return $sb->checkForOfp($user->id, Utils::generateNewId(), $flight->id);
    }

    /**
     * Read from the SimBrief URL
     */
    public function testReadSimbrief()
    {
        $user = factory(App\Models\User::class)->create();
        $briefing = $this->loadSimBrief($user);

        $this->assertNotEmpty($briefing->ofp_xml);
        $this->assertNotNull($briefing->xml);

        // Spot check reading of the files
        $files = $briefing->files;
        $this->assertEquals(45, $files->count());
        $this->assertEquals(
            'http://www.simbrief.com/ofp/flightplans/OMAAOMDB_PDF_1584226092.pdf',
            $files->firstWhere('name', 'PDF Document')['url']
        );

        // Spot check reading of images
        $images = $briefing->images;
        $this->assertEquals(5, $images->count());
        $this->assertEquals(
            'http://www.simbrief.com/ofp/uads/OMAAOMDB_1584226092_ROUTE.gif',
            $images->firstWhere('name', 'Route')['url']
        );

        $level = $briefing->xml->getFlightLevel();
        $this->assertEquals('380', $level);

        // Read the flight route
        $routeStr = $briefing->xml->getRouteString();
        $this->assertEquals(
            'DCT BOMUP DCT LOVIM DCT RESIG DCT NODVI DCT OBMUK DCT LORID DCT '.
            'ORGUR DCT PEBUS DCT EMOPO DCT LOTUK DCT LAGTA DCT LOVOL DCT',
            $routeStr
        );
    }

    public function testAttachToPirep()
    {
        $user = factory(App\Models\User::class)->create();
        $pirep = factory(App\Models\Pirep::class)->create([
            'user_id'        => $user->id,
            'dpt_airport_id' => 'OMAA',
            'arr_airport_id' => 'OMDB',
        ]);

        $briefing = $this->loadSimBrief($user);

        /** @var SimBriefService $sb */
        $sb = app(SimBriefService::class);
        $sb->attachSimbriefToPirep($pirep, $briefing);

        /*
         * Checks - ACARS entries for the route are loaded
         */
        $acars = Acars::where(['pirep_id' => $pirep->id, 'type' => AcarsType::ROUTE])->get();
        $this->assertEquals(12, $acars->count());

        $fix = $acars->firstWhere('name', 'BOMUP');
        $this->assertEquals($fix['name'], 'BOMUP');
        $this->assertEquals($fix['lat'], 24.484639);
        $this->assertEquals($fix['lon'], 54.578444);
        $this->assertEquals($fix['order'], 1);

        $briefing->refresh();

        $this->assertEmpty($briefing->flight_id);
        $this->assertEquals($pirep->id, $briefing->pirep_id);
    }

    /**
     * Test clearing expired briefs
     */
    public function testClearExpiredBriefs()
    {
        $user = factory(App\Models\User::class)->create();
        $sb_ignored = factory(SimBrief::class)->create([
            'user_id'    => $user->id,
            'flight_id'  => 'a_flight_id',
            'pirep_id'   => 'a_pirep_id',
            'created_at' => Carbon::now('UTC')->subDays(6)->toDateTimeString(),
        ]);

        factory(SimBrief::class)->create([
            'user_id'    => $user->id,
            'flight_id'  => 'a_flight_Id',
            'pirep_id'   => '',
            'created_at' => Carbon::now('UTC')->subDays(6)->toDateTimeString(),
        ]);

        /** @var SimBriefService $sb */
        $sb = app(SimBriefService::class);
        $sb->removeExpiredEntries();

        $all_briefs = SimBrief::all();
        $this->assertEquals(1, $all_briefs->count());
        $this->assertEquals($sb_ignored->id, $all_briefs[0]->id);
    }
}
