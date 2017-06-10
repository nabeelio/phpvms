<?php

use App\Models\Aircraft;
use App\Services\AircraftService;

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

    protected function get_ac_class()
    {
        return app('App\Repositories\AircraftClassRepository')
            ->findByField('code', 'H')->first();
    }

    protected function find_by_icao($icao)
    {
        $ac_repo = app('App\Repositories\AircraftRepository');
        return $ac_repo->findByICAO($icao);
    }

    protected function get_fare_by_code($code)
    {
        return app('App\Repositories\FareRepository')->findByCode($code);
    }

    /**
     * Check the association of the aircraft class to an aircraft
     * Mostly to experiment with the ORM type stuff. This isn't
     * where most of the testing, etc is required.
     */
    protected function add_aircraft()
    {
        $svc = app('App\Services\AircraftService');
        $err = $svc->create([
            'icao' => $this->ICAO,
            'name' => 'Boeing 777',
        ], $this->get_ac_class());

        $this->assertNotFalse($err);

        return $this->find_by_icao($this->ICAO);
    }

    public function testAircraftClasses()
    {
        $aircraft = $this->add_aircraft();
        $this->assertEquals($this->ICAO, $aircraft->icao, 'ICAO matching');
        $this->assertEquals(
            $this->get_ac_class(),
            $aircraft->class,
            'Check belongsTo relationship'
        );
    }

    public function testAircraftFaresNoOverride()
    {
        $fare_svc = app('App\Services\FareService');

        $aircraft = $this->add_aircraft();
        $fare = $this->get_fare_by_code('Y');

        $fare_svc->set_for_aircraft($aircraft, $fare);
        $ac_fares = $fare_svc->get_for_aircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals($fare->price, $ac_fares[0]->price);
        $this->assertEquals($fare->capacity, $ac_fares[0]->capacity);

        #
        # set an override now
        #
        $fare_svc->set_for_aircraft($aircraft, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        # look for them again
        $ac_fares = $fare_svc->get_for_aircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delete_from_aircraft($aircraft, $fare);
        $this->assertCount(0, $fare_svc->get_for_aircraft($aircraft));
    }

    public function testAircraftFaresOverride()
    {
        $fare_svc = app('App\Services\FareService');

        $aircraft = $this->add_aircraft();
        $fare = $this->get_fare_by_code('Y');

        $fare_svc->set_for_aircraft($aircraft, $fare, [
            'price' => 50, 'capacity' => 400
        ]);

        $ac_fares = $fare_svc->get_for_aircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(50, $ac_fares[0]->price);
        $this->assertEquals(400, $ac_fares[0]->capacity);

        #
        # update the override to a different amount and make sure it updates
        #

        $fare_svc->set_for_aircraft($aircraft, $fare, [
            'price' => 150, 'capacity' => 50
        ]);

        $ac_fares = $fare_svc->get_for_aircraft($aircraft);

        $this->assertCount(1, $ac_fares);
        $this->assertEquals(150, $ac_fares[0]->price);
        $this->assertEquals(50, $ac_fares[0]->capacity);

        # delete
        $fare_svc->delete_from_aircraft($aircraft, $fare);
        $this->assertCount(0, $fare_svc->get_for_aircraft($aircraft));
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
