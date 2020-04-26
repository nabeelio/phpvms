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
        $this->flightRepo = $flightRepo;
        $this->pirepRepo = $pirepRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    /**
     * Create a new airline, and initialize the journal
     *
     * @param array $attr
     *
     * @return \App\Models\Airline
     */
    public function createAirline(array $attr): Airline
    {
        $airline = $this->airlineRepo->create($attr);
        $airline->initJournal(setting('units.currency'));

        return $airline;
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
        // Check these asset counts in these repositories
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
