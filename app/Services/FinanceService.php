<?php
/**
 *
 */

namespace App\Services;

use App\Models\Enums\PirepSource;
use App\Models\Pirep;

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

    /**
     * Return the pilot's hourly pay for the given PIREP
     * @param Pirep $pirep
     */
    public function getPayForPirep(Pirep $pirep)
    {
        # Get the base rate for the rank
        $rank = $pirep->user->rank;
        if($pirep->source === PirepSource::ACARS) {
            $base_rate = $rank->acars_base_pay_rate;
        } else {
            $base_rate = $rank->manual_base_pay_rate;
        }
    }
}
