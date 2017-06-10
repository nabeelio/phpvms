<?php

use App\Models\Aircraft;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class AircraftTest extends TestCase
{
    protected $aircraft,
              $aircraft_class;

    public function setUp() {
        parent::setUp();
        $this->aircraft = $this->createRepository('AircraftRepository');
        $this->aircraft_class = $this->createRepository('AircraftClassRepository');
    }

    public function testAircraftClasses()
    {
        factory(App\Models\AircraftClass::class)->create();

        $this->aircraft->create([
            'aircraft_class_id' => 1,
            'icao' => 'B744',
            'name' => 'Boeing 747',
        ]);

        $aircraft = App\Models\Aircraft::where('icao', 'B744')->first();
        $this->assertEquals('B744', $aircraft->icao, 'ICAO matching');
        $this->assertEquals('H', $aircraft->class->code, 'Check belongsTo relationship');
    }
}
