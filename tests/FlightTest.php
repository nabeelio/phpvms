<?php


class FlightTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->addData('airline');
        $this->addData('airports');
    }

    public function addFlight()
    {
        $flight = new App\Models\Flight;
        $flight->airline_id = 1;
        $flight->flight_number = 100;
        $flight->dpt_airport_id = 1;
        $flight->arr_airport_id = 2;
        $flight->save();
    }

    /**
     * mainly to test the model relationships work correctly
     */
    public function testAddFlight()
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
