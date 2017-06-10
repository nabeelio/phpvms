<?php

namespace App\Services;

use App\Repositories\AircraftRepository;


class PIREPService {

    protected $aircraft;

    /**
     * return a PIREP model
     */
    public function __construct(
        AircraftRepository $aircraft
    ) {
        $this->aircraft = $aircraft;
    }

    public function create() {

    }
}
