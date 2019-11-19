<?php

use App\Services\AirlineService;

class AirlineTest extends TestCase
{
    protected $airlineSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');

        $this->airlineSvc = app(AirlineService::class);
    }

    /**
     * Try deleting an airline which has flights/other assets that exist
     */
    public function testDeleteAirlineWithFlight()
    {
        $airline = factory(App\Models\Airline::class)->create();
        factory(App\Models\Flight::class)->create([
            'airline_id' => $airline->id,
        ]);

        $this->assertFalse($this->airlineSvc->canDeleteAirline($airline));
    }

    /**
     * Try deleting an airline with existing PIREPs
     */
    public function testDeleteAirlineWithPirep()
    {
        $airline = factory(App\Models\Airline::class)->create();
        factory(App\Models\Pirep::class)->create([
            'airline_id' => $airline->id,
        ]);

        $this->assertFalse($this->airlineSvc->canDeleteAirline($airline));
    }
}
