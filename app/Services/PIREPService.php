<?php

namespace App\Services;

use App\Repositories\SubfleetRepository;


class PIREPService extends BaseService {

    protected $aircraft;

    /**
     * return a PIREP model
     */
    public function __construct(
        SubfleetRepository $aircraft
    ) {
        $this->aircraft = $aircraft;
    }

    public function create() {

    }
}
