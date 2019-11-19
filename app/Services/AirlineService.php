<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Airline;
use App\Repositories\AirlineRepository;
use App\Repositories\FlightRepository;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;

class AirlineService extends Service
{
    private $airlineRepo;
    private $flightRepo;
    private $pirepRepo;
    private $subfleetRepo;

    public function __construct(
        AirlineRepository $airlineRepo,
        FlightRepository $flightRepo,
        PirepRepository $pirepRepo,
        SubfleetRepository $subfleetRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->pirepRepo = $pirepRepo;
        $this->flightRepo = $flightRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    /**
     * Can the airline be deleted? Check if there are flights, etc associated with it
     *
     * @param Airline $airline
     *
     * @return bool
     */
    public function canDeleteAirline(Airline $airline): bool
    {
        // Check these asset counts to see if the airline exists for any of these
        $repos = [
            $this->pirepRepo,
            $this->flightRepo,
            $this->subfleetRepo,
        ];

        $w = ['airline_id' => $airline->id];
        foreach ($repos as $repo) {
            if ($repo->count($w) > 0) {
                return false;
            }
        }

        return true;
    }
}
