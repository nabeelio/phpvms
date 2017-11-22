<?php


class FlightTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->addData('base');
    }

    public function addFlight()
    {
        $flight = new App\Models\Flight;
        $flight->airline_id = 1;
        $flight->flight_number = 10;
        $flight->dpt_airport_id = 1;
        $flight->arr_airport_id = 2;
        $flight->save();
        return $flight->id;
    }

    public function testGetFlight()
    {
        $flight_id = $this->addFlight();
        $response = $this->json('GET', '/api/flight/'.$flight_id);
        $response->assertStatus(200);
        $response->assertJson(['data' => true]);
    }

    /**
     * mainly to test the model relationships work correctly
     */
    public function XtestAddFlight()
    {
        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );

        $this->addFlight();

        $flight = App\Models\Flight::where('flight_number', 100)->first();

        $this->assertEquals($flight->dpt_airport->icao, 'KAUS');
        $this->assertEquals($flight->arr_airport->icao, 'KJFK');
    }
}
