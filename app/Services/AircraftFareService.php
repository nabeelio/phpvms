<?php

namespace App\Services;

use App\Repositories\FareRepository;
use App\Repositories\AircraftRepository;


class AircraftFareService {

    protected $aircraft, $fare;

    /**
     * return a PIREP model
     * @param $aircraft AircraftRepository
     * @param $fare FareRepository
     */
    public function __construct(AircraftRepository $aircraft, FareRepository $fare) {
        $this->fare = $fare;
        $this->aircraft = $aircraft;
    }

    public function link(int $aircraft_id, int $fare_id) {

    }

    public function unlink(int $aircraft_id, int $fare_id) {

    }
}
