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
        $this->get('/api/flights/'.$flight_id, self::$auth_headers)
            ->assertStatus(200)
            ->assertJson(['dpt_airport_id' => 'KAUS']);

        $this->get('/api/flights/INVALID', self::$auth_headers)
            ->assertStatus(404);
    }

    /**
     * Search based on all different criteria
     */
    public function testSearchFlight()
    {
        $flight_id = $this->addFlight();

        # search specifically for a flight ID
        $query = 'flight_id='.$flight_id;
        $req = $this->get('/api/flights/search?' . $query, self::$auth_headers);
        $req->assertStatus(200);
    }
}
