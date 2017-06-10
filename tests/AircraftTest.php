<?php

use App\Models\Aircraft;


class AircraftTest extends TestCase
{
    protected $aircraft, $aircraft_class;

    public function setUp() {
        parent::setUp();
        $this->aircraft = $this->createRepository('AircraftRepository');
        $this->aircraft_class = $this->createRepository('AircraftClassRepository');

        # add an aircraft_class
        factory(App\Models\AircraftClass::class)->create();
    }

    protected function add_fares(Aircraft $aircraft) {

    }

    public function testAircraftClasses()
    {
        # add a few fare classes

        $this->aircraft->create([
            'aircraft_class_id' => 1,
            'icao' => 'B777',
            'name' => 'Boeing 777',
        ]);

        $aircraft = Aircraft::where('icao', 'B777')->first();
        $this->assertEquals('B777', $aircraft->icao, 'ICAO matching');
        $this->assertEquals('H', $aircraft->class->code, 'Check belongsTo relationship');

        // check to see if the fares are properly applied to this aircraft
        $this->add_fares($aircraft);
    }
}
