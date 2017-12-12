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
        $flight->dpt_airport_id = 'KAUS';
        $flight->arr_airport_id = 'KJFK';
        $flight->save();

        return $flight->id;
    }

    public function testGetFlight()
    {
        $flight_id = $this->addFlight();
        $this->get('/api/flight/'.$flight_id, self::$auth_headers)
            ->assertStatus(200);
    }
}
