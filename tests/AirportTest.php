<?php

namespace Tests;

use App\Models\Airport;

class AirportTest extends TestCase
{
    public function testSavingAirportFromApiResponse()
    {
        // This is the response from the API
        $airportResponse = [
            'icao'    => 'KJFK',
            'iata'    => 'JFK',
            'name'    => 'John F Kennedy International Airport',
            'city'    => 'New York',
            'country' => 'United States',
            'tz'      => 'America/New_York',
            'lat'     => 40.63980103,
            'lon'     => -73.77890015,
        ];

        $airport = new Airport($airportResponse);
        $this->assertEquals($airportResponse['icao'], $airport->icao);
        $this->assertEquals($airportResponse['tz'], $airport->timezone);
    }
}
