<?php
/**
 *
 */

namespace App\Services;

class FinanceService extends BaseService
{
    private $fareSvc,
            $flightSvc;

    /**
     * FinanceService constructor.
     * @param FareService $fareSvc
     * @param FlightService $flightSvc
     */
    public function __construct(
        FareService $fareSvc,
        FlightService $flightSvc
    ) {
        $this->fareSvc = $fareSvc;
        $this->flightSvc = $flightSvc;
    }
}
