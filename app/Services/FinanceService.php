<?php
/**
 *
 */

namespace App\Services;

use App\Models\Enums\PirepSource;
use App\Models\Pirep;
use App\Support\Math;
use App\Support\Money;

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
     * @return float
     * @throws \Money\Exception\UnknownCurrencyException
     * @throws \InvalidArgumentException
     */
    public function getPayRateForPirep(Pirep $pirep)
    {
        # Get the base rate for the rank
        $rank = $pirep->user->rank;
        $subfleet_id = $pirep->aircraft->subfleet_id;

        # find the right subfleet
        $override_rate = $rank->subfleets()
            ->where('subfleet_id', $subfleet_id)
            ->first()
            ->pivot;

        if($pirep->source === PirepSource::ACARS) {
            $base_rate = $rank->acars_base_pay_rate;
            $override_rate = $override_rate->acars_pay;
        } else {
            $base_rate = $rank->manual_base_pay_rate;
            $override_rate = $override_rate->manual_pay;
        }

        if(!$override_rate) {
            return $base_rate;
        }

        # Not a percentage override
        if(substr_count($override_rate, '%') === 0) {
            return $override_rate;
        }

        return Math::addPercent($base_rate, $override_rate);
    }

    /**
     * Get the user's payment amount for a PIREP
     * @param Pirep $pirep
     * @return Money
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Money\Exception\UnknownCurrencyException
     */
    public function getPilotPilotPay(Pirep $pirep)
    {
        $pilot_rate = $this->getPayRateForPirep($pirep) / 60;
        $payment = round($pirep->flight_time * $pilot_rate, 2);

        return Money::createFromAmount($payment);
    }
}
