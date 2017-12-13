<?php

use App\Models\Fare;
use App\Models\Subfleet;

class AircraftTest extends TestCase
{
    protected $ac_svc,
              $ICAO = 'B777';

    public function setUp()
    {
        parent::setUp();
        $this->addData('aircraft');
    }

    protected function getAircraftClass()
    {
        return app('App\Repositories\AircraftClassRepository')
            ->findByField('code', 'H')->first();
    }

    protected function findByICAO($icao)
    {
        $ac_repo = app('App\Repositories\SubfleetRepository');
        return $ac_repo->findByICAO($icao);
    }

    protected function getFareByCode($code)
    {
        return Fare::where('code', $code)->first();
    }

    public function testSubfleetFaresNoOverride()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        return true;
        $fare_svc = app('App\Services\FareService');

        $subfleet = Subfleet::find(1);
        $fare = $this->getFareByCode('Y');

        $fare_svc->setForAircraft($subfleet, $fare);
        $ac_fares = $fare_svc->getForAircraft($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($fare->price, $ac_fares[0]->price);
        $this->assertEquals($fare->capacity, $ac_fares[0]->capacity);

        #
        # set an override now
        #
        $fare_svc->setForAircraft($subfleet, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        # look for them again
        $ac_fares = $fare_svc->getForAircraft($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delFromAircraft($subfleet, $fare);
        $this->assertCount(0, $fare_svc->getForAircraft($subfleet));
    }

    public function testSubfleetFaresOverride()
    {
        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );

        $fare_svc = app('App\Services\FareService');

        $subfleet = Subfleet::find(1);
        $fare = $this->getFareByCode('Y');

        $fare_svc->setForAircraft($subfleet, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        $ac_fares = $fare_svc->getForAircraft($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        #
        # update the override to a different amount and make sure it updates
        #

        $fare_svc->setForAircraft($subfleet, $fare, [
            'price' => 150, 'capacity' => 50
        ]);

        $ac_fares = $fare_svc->getForAircraft($subfleet);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(150, $ac_fares[0]->price);
        $this->assertEquals(50, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delFromAircraft($subfleet, $fare);
        $this->assertCount(0, $fare_svc->getForAircraft($subfleet));
    }

    /**
     * @expectedException Exception
     */
    public function testAircraftMissingField()
    {
        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );
    }
}
