<?php

namespace Tests;

use App\Services\AirlineService;

class AirlineTest extends TestCase
{
    /** @var AirlineService */
    protected $airlineSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->addData('base');

        $this->airlineSvc = app(AirlineService::class);
    }

    public function testAddAirline()
    {
        $attrs = \App\Models\Airline::factory()->make([
            'iata' => '',
        ])->toArray();

        $airline = $this->airlineSvc->createAirline($attrs);
        $this->assertNotNull($airline);

        // Add another airline, also blank IATA
        $attrs = \App\Models\Airline::factory()->make([
            'iata' => '',
        ])->toArray();
        $airline = $this->airlineSvc->createAirline($attrs);
        $this->assertNotNull($airline);
    }

    /**
     * Try deleting an airline which has flights/other assets that exist
     */
    public function testDeleteAirlineWithFlight()
    {
        $airline = \App\Models\Airline::factory()->create();
        \App\Models\Flight::factory()->create([
            'airline_id' => $airline->id,
        ]);

        $this->assertFalse($this->airlineSvc->canDeleteAirline($airline));
    }

    /**
     * Try deleting an airline with existing PIREPs
     */
    public function testDeleteAirlineWithPirep()
    {
        $airline = \App\Models\Airline::factory()->create();
        \App\Models\Pirep::factory()->create([
            'airline_id' => $airline->id,
        ]);

        $this->assertFalse($this->airlineSvc->canDeleteAirline($airline));
    }
}
