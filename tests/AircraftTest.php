<?php


class AircraftTest extends TestCase
{
    protected $ac_svc,
              $ICAO = 'B777';

    public function setUp()
    {
        parent::setUp();
        $this->setup_data();
    }

    /**
     * add the fares to a given aircraft
     * run the factory for incl the fares
     */
    protected function setup_data()
    {
        factory(App\Models\AircraftClass::class)->create();
        factory(App\Models\Fare::class)->create();
    }

    protected function getAircraftClass()
    {
        return app('App\Repositories\AircraftClassRepository')
            ->findByField('code', 'H')->first();
    }

    protected function findByICAO($icao)
    {
        $ac_repo = app('App\Repositories\AircraftRepository');
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
        $svc = app('App\Services\AircraftService');
        $err = $svc->create([
            'icao' => $this->ICAO,
            'name' => 'Boeing 777',
        ], $this->getAircraftClass());

        $this->assertNotFalse($err);

        return $this->findByICAO($this->ICAO);
    }

    public function testAircraftClasses()
    {
        $aircraft = $this->addAircraft();
        $this->assertEquals($this->ICAO, $aircraft->icao, 'ICAO matching');
        $this->assertEquals(
            $this->getAircraftClass(),
            $aircraft->class,
            'Check belongsTo relationship'
        );
    }

    public function testAircraftFaresNoOverride()
    {
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
        # missing the name field
        $svc = app('App\Services\AircraftService');
        $svc->create(['icao' => $this->ICAO]);
    }
}
