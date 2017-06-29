<?php

namespace App\Services;

use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;


class PIREPService extends BaseService {

    protected $aircraftRepo, $pirepRepo;

    /**
     * return a PIREP model
     */
    public function __construct(
        SubfleetRepository $aircraftRepo,
        PirepRepository $pirepRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function create() {

    }
}
