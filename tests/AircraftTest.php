<?php


class AircraftTest extends TestCase
{
    protected $ac_svc,
              $ICAO = 'B777';

    public function setUp()
    {
        parent::setUp();
        $this->addData('aircraft_test');
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
        return app('App\Repositories\FareRepository')->findByCode($code);
    }

    /**
     * Check the association of the aircraft class to an aircraft
     * Mostly to experiment with the ORM type stuff. This isn't
     * where most of the testing, etc is required.
     */
    protected function addAircraft()
    {
        $mdl = new App\Models\Aircraft;
        $mdl->icao = $this->ICAO;
        $mdl->name = 'Boeing 777';
        $mdl->save();

        return $this->findByICAO($this->ICAO);
    }

    public function testAircraftFaresNoOverride()
    {
        return true;
        $fare_svc = app('App\Services\FareService');

        $aircraft = $this->addAircraft();
        $fare = $this->getFareByCode('Y');

        $fare_svc->setForAircraft($aircraft, $fare);
        $ac_fares = $fare_svc->getForAircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($fare->price, $ac_fares[0]->price);
        $this->assertEquals($fare->capacity, $ac_fares[0]->capacity);

        #
        # set an override now
        #
        $fare_svc->setForAircraft($aircraft, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        # look for them again
        $ac_fares = $fare_svc->getForAircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delFromAircraft($aircraft, $fare);
        $this->assertCount(0, $fare_svc->getForAircraft($aircraft));
    }

    public function testAircraftFaresOverride()
    {
        return true;
        $fare_svc = app('App\Services\FareService');

        $aircraft = $this->addAircraft();
        $fare = $this->getFareByCode('Y');

        $fare_svc->setForAircraft($aircraft, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        $ac_fares = $fare_svc->getForAircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        #
        # update the override to a different amount and make sure it updates
        #

        $fare_svc->setForAircraft($aircraft, $fare, [
            'price' => 150, 'capacity' => 50
        ]);

        $ac_fares = $fare_svc->getForAircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(150, $ac_fares[0]->price);
        $this->assertEquals(50, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delFromAircraft($aircraft, $fare);
        $this->assertCount(0, $fare_svc->getForAircraft($aircraft));
    }

    /**
     * @expectedException Exception
     */
    public function testAircraftMissingField()
    {
        return true;
        # missing the name field
        $svc = app('App\Services\AircraftService');
        $svc->create(['icao' => $this->ICAO]);
    }
}
