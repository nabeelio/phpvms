<?php

namespace Tests;

use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\SimBrief;
use App\Services\SimBriefService;
use App\Support\Utils;
use Carbon\Carbon;

class SimBriefTest extends TestCase
{
    private static $simbrief_flight_id = 'simbriefflightid';

    /**
     * Load SimBrief
     *
     * @param \App\Models\User $user
     *
     * @return \App\Models\SimBrief
     */
    protected function loadSimBrief($user): SimBrief
    {
        $flight = factory(Flight::class)->create([
            'id'             => self::$simbrief_flight_id,
            'dpt_airport_id' => 'OMAA',
            'arr_airport_id' => 'OMDB',
        ]);

        $this->mockXmlResponse([
            'simbrief/briefing.xml',
            'simbrief/acars_briefing.xml',
        ]);

        /** @var SimBriefService $sb */
        $sb = app(SimBriefService::class);

        return $sb->checkForOfp($user->id, Utils::generateNewId(), $flight->id);
    }

    /**
     * Read from the SimBrief URL
     */
    public function testReadSimbrief()
    {
        $this->user = $this->createUser();
        $briefing = $this->loadSimBrief($this->user);

        $this->assertNotEmpty($briefing->ofp_xml);
        $this->assertNotNull($briefing->xml);

        // Spot check reading of the files
        $files = $briefing->files;
        $this->assertEquals(47, $files->count());
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

    /**
     * Check that the API calls are working (simbrief in the response, can retrieve the briefing)
     */
    public function testApiCalls()
    {
        $this->user = $this->createUser();
        $briefing = $this->loadSimBrief($this->user);

        // Check the flight API response
        $response = $this->get('/api/flights/'.$briefing->flight_id);
        $response->assertOk();
        $flight = $response->json('data');

        $this->assertNotNull($flight['simbrief']);
        $this->assertEquals($briefing->id, $flight['simbrief']['id']);

        $url = str_replace('http://', 'https://', $flight['simbrief']['url']);
        $this->assertEquals(
            'https://localhost/api/flights/'.$briefing->id.'/briefing',
            $url
        );

        // Retrieve the briefing via API, and then check the doctype
        $response = $this->get('/api/flights/'.$briefing->id.'/briefing');
        $response->assertOk();
        // $response->assertHeader('Content-Type', 'application/xml');

        $xml = simplexml_load_string($response->content());
        $this->assertNotNull($xml);

        $this->assertEquals('VMSAcars', $xml->getName());
        $this->assertEquals('FlightPlan', $xml->attributes()->Type);
    }

    /**
     * Make sure the user's bids have the Simbrief data show up
     */
    public function testUserBidSimbrief()
    {
        $this->user = $this->createUser();
        $this->loadSimBrief($this->user);

        // Find the flight
        $uri = '/api/user/bids';
        $data = ['flight_id' => self::$simbrief_flight_id];

        $body = $this->put($uri, $data);
        $body = $body->json('data');

        // Make sure Simbrief is there
        $this->assertNotNull($body['flight']['simbrief']['id']);
    }

    public function testAttachToPirep()
    {
        $user = $this->createUser();
        $pirep = factory(Pirep::class)->create([
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
        $user = $this->createUser();
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
